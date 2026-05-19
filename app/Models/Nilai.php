<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Nilai
{
    public function getTahunAjaran(): array
    {
        $stmt = Database::connect()->query(
            'SELECT DISTINCT tahun_ajaran FROM riwayat_kelas ORDER BY tahun_ajaran DESC'
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getKelas(string $tahunAjaran = ''): array
    {
        if ($tahunAjaran !== '') {
            $stmt = Database::connect()->prepare(
                'SELECT DISTINCT kelas FROM riwayat_kelas WHERE tahun_ajaran = :ta ORDER BY kelas ASC'
            );
            $stmt->execute([':ta' => $tahunAjaran]);
        } else {
            $stmt = Database::connect()->query(
                'SELECT DISTINCT kelas FROM riwayat_kelas ORDER BY kelas ASC'
            );
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getSemester(string $tahunAjaran = '', string $kelas = ''): array
    {
        $db = Database::connect();
        $where = [];
        $params = [];
        
        if ($tahunAjaran !== '') {
            $where[] = 'tahun_ajaran = :ta';
            $params[':ta'] = $tahunAjaran;
        }
        if ($kelas !== '') {
            $where[] = 'kelas = :kelas';
            $params[':kelas'] = $kelas;
        }
        
        $whereSql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $db->prepare("SELECT DISTINCT semester FROM riwayat_kelas $whereSql ORDER BY semester ASC");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getRekapNilai(string $tahunAjaran = '', string $kelas = '', string $semester = '', int $limit = 10, int $offset = 0, string $status = '', string $tingkat = '', string $search = ''): array
    {
        $db = Database::connect();
        $where = [];
        $params = [];

        if ($tahunAjaran !== '') {
            $where[] = 'rk.tahun_ajaran = :ta';
            $params[':ta'] = $tahunAjaran;
        }
        if ($kelas !== '') {
            $where[] = 'rk.kelas = :kelas';
            $params[':kelas'] = $kelas;
        }
        if ($semester !== '') {
            $where[] = 'rk.semester = :sem';
            $params[':sem'] = $semester;
        }
        if ($tingkat !== '') {
            $where[] = "(rk.kelas LIKE :tingkat OR rk.kelas LIKE :tingkat2)";
            $params[':tingkat'] = $tingkat . ' %';
            $params[':tingkat2'] = (str_replace('XII', '12', str_replace('XI', '11', str_replace('X', '10', $tingkat)))) . ' %';
        }

        // Search
        if ($search !== '') {
            $where[] = "(s.nama LIKE :q OR s.nisn LIKE :q OR s.nis LIKE :q OR rk.kelas LIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }

        $whereSql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        // Jika tidak ada filter status, defaultnya hanya tampilkan yang sudah ada datanya.
        // Tapi jika status = 'incomplete', kita tampilkan yang datanya belum lengkap (termasuk yang 0).
        if ($status === 'incomplete') {
            $havingSql = "HAVING (jumlah_mapel_diisi < 16 OR id_absensi IS NULL)";
        } else {
            // Default behaviour: Only show if they have AT LEAST some data
            $hasNilaiSql = "
                (
                    EXISTS (SELECT 1 FROM nilai n2 WHERE n2.id_riwayat = rk.id_riwayat)
                    OR EXISTS (SELECT 1 FROM absensi a2 WHERE a2.id_riwayat = rk.id_riwayat)
                    OR EXISTS (SELECT 1 FROM ekstrakurikuler e2 WHERE e2.id_riwayat = rk.id_riwayat)
                    OR EXISTS (SELECT 1 FROM prestasi p2 WHERE p2.id_riwayat = rk.id_riwayat)
                )
            ";
            $where[] = $hasNilaiSql;
            $whereSql = 'WHERE ' . implode(' AND ', $where);
            $havingSql = "";
        }

        // Hitung Total (agak kompleks karena ada HAVING)
        $countSql = "
            SELECT COUNT(*) FROM (
                SELECT rk.id_riwayat, COUNT(n.id_nilai) as jumlah_mapel_diisi, a.id_absensi
                FROM riwayat_kelas rk
                JOIN siswa s ON rk.id_siswa = s.id_siswa
                LEFT JOIN nilai n ON rk.id_riwayat = n.id_riwayat
                LEFT JOIN absensi a ON rk.id_riwayat = a.id_riwayat
                $whereSql
                GROUP BY rk.id_riwayat
                $havingSql
            ) tmp
        ";
        $countStmt = $db->prepare($countSql);
        foreach ($params as $k => $v) $countStmt->bindValue($k, $v);
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        // Ambil data
        $sql = "
            SELECT 
                rk.id_riwayat, 
                s.nama, 
                s.nisn, 
                s.nis, 
                rk.tahun_ajaran, 
                rk.kelas, 
                rk.semester,
                SUM(n.nilai) as total_nilai,
                COUNT(n.id_nilai) as jumlah_mapel_diisi,
                (
                    SELECT COUNT(*) FROM mata_pelajaran mp 
                    WHERE mp.tingkat = (CASE WHEN rk.kelas LIKE 'XII%' THEN 'XII' WHEN rk.kelas LIKE 'XI%' THEN 'XI' ELSE 'X' END)
                      AND mp.jurusan = (CASE WHEN rk.kelas LIKE '%MIPA%' THEN 'MIPA' ELSE 'IPS' END)
                ) as total_mapel_seharusnya,
                a.id_absensi
            FROM riwayat_kelas rk
            JOIN siswa s ON rk.id_siswa = s.id_siswa
            LEFT JOIN nilai n ON rk.id_riwayat = n.id_riwayat
            LEFT JOIN absensi a ON rk.id_riwayat = a.id_riwayat
            $whereSql
            GROUP BY
                rk.id_riwayat,
                s.nama,
                s.nisn,
                s.nis,
                rk.tahun_ajaran,
                rk.kelas,
                rk.semester,
                a.id_absensi
            $havingSql
            ORDER BY s.nama ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return [
            'data' => $data,
            'total' => $total
        ];
    }
}
