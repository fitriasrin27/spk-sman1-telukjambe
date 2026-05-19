<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Dashboard
{
    public function totalSiswa(): int
    {
        return (int) Database::connect()
            ->query('SELECT COUNT(*) FROM siswa')
            ->fetchColumn();
    }

    public function totalSiswaKelasXii(): int
    {
        $db = Database::connect();
        
        // Ambil TA terbaru
        $ta = $db->query("SELECT tahun_ajaran FROM riwayat_kelas ORDER BY tahun_ajaran DESC LIMIT 1")->fetchColumn();
        if (!$ta) return 0;

        $statement = $db->prepare(
            "SELECT COUNT(DISTINCT id_siswa) FROM riwayat_kelas 
             WHERE (kelas LIKE 'XII %' OR kelas LIKE '12 %') AND tahun_ajaran = ?"
        );
        $statement->execute([$ta]);
        $totalXii = (int) $statement->fetchColumn();

        // Kembalikan 40% (pembulatan ke atas)
        return (int) ceil($totalXii * 0.40);
    }

    public function distribusiEligible(): array
    {
        $db = Database::connect();
        
        // Ambil TA terbaru
        $ta = $db->query("SELECT tahun_ajaran FROM riwayat_kelas ORDER BY tahun_ajaran DESC LIMIT 1")->fetchColumn();
        if (!$ta) return ['MIPA' => 0, 'IPS' => 0];

        $data = ['MIPA' => 0, 'IPS' => 0];

        foreach (['MIPA', 'IPS'] as $jurusan) {
            $statement = $db->prepare(
                "SELECT COUNT(DISTINCT id_siswa) FROM riwayat_kelas 
                 WHERE (kelas LIKE 'XII %' OR kelas LIKE '12 %') 
                   AND kelas LIKE ? 
                   AND tahun_ajaran = ?"
            );
            $statement->execute(['%' . $jurusan . '%', $ta]);
            $total = (int) $statement->fetchColumn();
            $data[$jurusan] = (int) ceil($total * 0.40);
        }

        return $data;
    }

    public function getKesiapanData(?string $tahunAjaran = null, ?int $semester = null): array
    {
        $db = Database::connect();
        if (!$tahunAjaran) {
            $tahunAjaran = $db->query("SELECT tahun_ajaran FROM riwayat_kelas ORDER BY tahun_ajaran DESC LIMIT 1")->fetchColumn();
        }
        // $semester di sini 1 (Ganjil) atau 2 (Genap)
        if ($semester === null) $semester = 1; 
        
        if (!$tahunAjaran) return [];

        // Pemetaan: Ganjil -> (1, 3, 5), Genap -> (2, 4, 6)
        $semList = ($semester == 1) ? [1, 3, 5] : [2, 4, 6];
        $placeholders = implode(',', array_fill(0, count($semList), '?'));

        $sql = "
            SELECT 
                tingkat, jurusan, COUNT(*) as total,
                SUM(CASE WHEN jml_nilai >= 16 AND has_absen > 0 THEN 1 ELSE 0 END) as lengkap
            FROM (
                SELECT 
                    rk.id_siswa, rk.semester,
                    CASE 
                        WHEN rk.kelas LIKE 'X %' THEN 'X'
                        WHEN rk.kelas LIKE 'XI %' AND rk.kelas NOT LIKE 'XII %' THEN 'XI'
                        WHEN rk.kelas LIKE 'XII %' THEN 'XII'
                        ELSE 'Lainnya'
                    END as tingkat,
                    CASE 
                        WHEN rk.kelas LIKE '%MIPA%' THEN 'MIPA'
                        WHEN rk.kelas LIKE '%IPS%' THEN 'IPS'
                        ELSE 'IPS'
                    END as jurusan,
                    (SELECT COUNT(*) FROM nilai n WHERE n.id_riwayat = rk.id_riwayat) as jml_nilai,
                    (SELECT COUNT(*) FROM absensi a WHERE a.id_riwayat = rk.id_riwayat) as has_absen
                FROM riwayat_kelas rk
                WHERE rk.tahun_ajaran = ? AND rk.semester IN ($placeholders)
            ) sub
            WHERE tingkat != 'Lainnya'
            AND (
                (tingkat = 'X' AND semester IN (1, 2)) OR
                (tingkat = 'XI' AND semester IN (3, 4)) OR
                (tingkat = 'XII' AND semester IN (5, 6))
            )
            GROUP BY tingkat, jurusan
        ";

        $stmt = $db->prepare($sql);
        $params = array_merge([$tahunAjaran], $semList);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $data = [];
        foreach ($rows as $r) {
            $data[$r['tingkat']][$r['jurusan']] = [
                'total'   => (int)$r['total'],
                'lengkap' => (int)$r['lengkap']
            ];
        }

        return $data;
    }

    public function getDaftarTahunAjaran(): array
    {
        return Database::connect()
            ->query("SELECT DISTINCT tahun_ajaran FROM riwayat_kelas ORDER BY tahun_ajaran DESC")
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getProgresRankingKelas(?string $tahunAjaran = null, ?int $semester = null): array
    {
        $db = Database::connect();
        if (!$tahunAjaran) {
            $tahunAjaran = $db->query("SELECT tahun_ajaran FROM riwayat_kelas ORDER BY tahun_ajaran DESC LIMIT 1")->fetchColumn();
        }
        if (!$tahunAjaran) return ['total' => 0, 'selesai' => 0, 'persen' => 0, 'semester' => 0, 'jurusan' => []];

        // $semester di sini 1 (Ganjil) atau 2 (Genap)
        if ($semester === null) $semester = 1;

        $semList = ($semester == 1) ? [1, 3, 5] : [2, 4, 6];
        $placeholders = implode(',', array_fill(0, count($semList), '?'));

        $result = [
            'total'    => 0,
            'selesai'  => 0,
            'persen'   => 0,
            'semester' => $semester,
            'jurusan'  => [
                'MIPA' => ['total' => 0, 'selesai' => 0, 'persen' => 0],
                'IPS'  => ['total' => 0, 'selesai' => 0, 'persen' => 0]
            ]
        ];

        foreach (['MIPA', 'IPS'] as $jur) {
            // Total Kelas per Jurusan (Sesuaikan Semester)
            $stmtTotal = $db->prepare("SELECT COUNT(DISTINCT kelas) FROM riwayat_kelas WHERE tahun_ajaran = ? AND semester IN ($placeholders) AND kelas LIKE ?");
            $stmtTotal->execute(array_merge([$tahunAjaran], $semList, ['%' . $jur . '%']));
            $total = (int) $stmtTotal->fetchColumn();

            // Selesai per Jurusan (Sesuaikan semester_target)
            $stmtSelesai = $db->prepare("
                SELECT COUNT(DISTINCT kelas) FROM perhitungan 
                WHERE jenis_perhitungan = 'peringkat_kelas' 
                  AND tahun_ajaran = ? 
                  AND semester_target IN ($placeholders)
                  AND jurusan = ?
            ");
            $stmtSelesai->execute(array_merge([$tahunAjaran], $semList, [$jur]));
            $selesai = (int) $stmtSelesai->fetchColumn();

            $result['jurusan'][$jur] = [
                'total'   => $total,
                'selesai' => $selesai,
                'persen'  => $total > 0 ? round(($selesai / $total) * 100) : 0
            ];

            $result['total'] += $total;
            $result['selesai'] += $selesai;
        }

        $result['persen'] = $result['total'] > 0 ? round(($result['selesai'] / $result['total']) * 100) : 0;

        return $result;
    }

    public function getKriteria(): array
    {
        return Database::connect()
            ->query("SELECT kode_kriteria, nama_kriteria, bobot, atribut FROM kriteria ORDER BY kode_kriteria ASC")
            ->fetchAll();
    }
}
