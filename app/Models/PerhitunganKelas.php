<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class PerhitunganKelas
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    // =========================================================
    // OPSI FILTER
    // =========================================================

    public function getTahunAjaran(): array
    {
        $stmt = $this->db->query('SELECT DISTINCT tahun_ajaran FROM riwayat_kelas ORDER BY tahun_ajaran DESC');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getKelas(string $tahunAjaran = ''): array
    {
        if ($tahunAjaran !== '') {
            $stmt = $this->db->prepare('SELECT DISTINCT kelas FROM riwayat_kelas WHERE tahun_ajaran = :ta ORDER BY kelas ASC');
            $stmt->execute([':ta' => $tahunAjaran]);
        } else {
            $stmt = $this->db->query('SELECT DISTINCT kelas FROM riwayat_kelas ORDER BY kelas ASC');
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // =========================================================
    // DATA SISWA PER KELAS (agregat C1–C4 dari semua semester)
    // =========================================================

    /**
     * Kumpulkan data C1–C4 setiap siswa dalam satu kelas
     * hanya dari semester yang dipilih (bukan akumulasi).
     */
    public function getDataSiswaUntukSAW(string $tahunAjaran, string $kelas, int $semester): array
    {
        $tingkat = (strpos($kelas, 'XII') === 0) ? 'XII' : ((strpos($kelas, 'XI') === 0) ? 'XI' : 'X');
        $jurusan = (strpos($kelas, 'MIPA') !== false) ? 'MIPA' : 'IPS';

        // 1. Hitung jumlah mapel seharusnya untuk tingkat & jurusan ini
        $stmtMapel = $this->db->prepare("SELECT COUNT(*) FROM mata_pelajaran WHERE tingkat = ? AND jurusan = ?");
        $stmtMapel->execute([$tingkat, $jurusan]);
        $totalMapelKelas = (int) ($stmtMapel->fetchColumn() ?: 0);

        // 2. Ambil data agregat C1-C4 dalam satu query
        // C1: Total Nilai Akademik
        // C2: Total Absensi (Sakit + Izin + Alpa)
        // C3: Total Poin Ekskul (Konversi)
        // C4: Total Poin Prestasi (Konversi)
        $sql = "
            SELECT s.id_siswa, s.nama, s.nisn, s.nis, rk.id_riwayat,
                   (SELECT COALESCE(SUM(n.nilai), 0) FROM nilai n WHERE n.id_riwayat = rk.id_riwayat) as c1_raw,
                   (SELECT COALESCE(a.sakit + a.izin + a.alpa, 0) FROM absensi a WHERE a.id_riwayat = rk.id_riwayat) as c2_raw,
                   (SELECT COALESCE(SUM(kv.nilai_konversi), 0) 
                    FROM ekstrakurikuler e 
                    JOIN konversi_nilai kv ON kv.nilai_asli = e.predikat 
                    JOIN kriteria kr ON kr.id_kriteria = kv.id_kriteria AND kr.kode_kriteria = 'C3'
                    WHERE e.id_riwayat = rk.id_riwayat) as c3_raw,
                   (SELECT COALESCE(SUM(kv.nilai_konversi), 0) 
                    FROM prestasi p 
                    JOIN konversi_nilai kv ON kv.nilai_asli = p.tingkat 
                    JOIN kriteria kr ON kr.id_kriteria = kv.id_kriteria AND kr.kode_kriteria = 'C4'
                    WHERE p.id_riwayat = rk.id_riwayat) as c4_raw
            FROM riwayat_kelas rk
            JOIN siswa s ON s.id_siswa = rk.id_siswa
            WHERE rk.tahun_ajaran = :ta AND rk.kelas = :kelas AND rk.semester = :sem
            ORDER BY s.nama ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ta' => $tahunAjaran, ':kelas' => $kelas, ':sem' => $semester]);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $r) {
            $rataRata = $totalMapelKelas > 0 ? (float)$r['c1_raw'] / $totalMapelKelas : 0;
            
            $result[] = [
                'id_siswa'  => (int) $r['id_siswa'],
                'nama'      => $r['nama'],
                'nisn'      => $r['nisn'],
                'nis'       => $r['nis'],
                'c1'        => (float) $r['c1_raw'],
                'c2'        => (float) $r['c2_raw'],
                'c3'        => (float) $r['c3_raw'],
                'c4'        => (float) $r['c4_raw'],
                'rata_rata' => $rataRata,
            ];
        }

        return $result;
    }

    // =========================================================
    // ALGORITMA SAW
    // =========================================================

    private function checkColumns(): void
    {
        $cols = ['bobot_c1', 'bobot_c2', 'bobot_c3', 'bobot_c4'];
        foreach ($cols as $col) {
            $check = $this->db->query("SHOW COLUMNS FROM perhitungan LIKE '$col'")->fetch();
            if (!$check) {
                $this->db->exec("ALTER TABLE perhitungan ADD COLUMN $col DECIMAL(5,2) DEFAULT 0");
            }
        }
    }

    /**
     * Hitung normalisasi + preferensi SAW dan simpan batch ke database.
     * Mengembalikan ['id_perhitungan' => int, 'hasil' => array].
     */
    public function hitungSAW(
        array  $dataSiswa,
        string $tahunAjaran,
        string $kelas,
        int    $semester,
        int    $idUser
    ): array {
        $this->checkColumns(); // Auto-migration
        if (empty($dataSiswa)) return [];

        // Ambil bobot dari tabel kriteria
        $stmtBobot = $this->db->query("SELECT kode_kriteria, bobot FROM kriteria ORDER BY kode_kriteria ASC");
        $bobotMap  = [];
        foreach ($stmtBobot->fetchAll() as $row) {
            $bobotMap[$row['kode_kriteria']] = (float) $row['bobot'];
        }
        $w1 = $bobotMap['C1'] ?? 0.90;
        $w2 = $bobotMap['C2'] ?? 0.05;
        $w3 = $bobotMap['C3'] ?? 0.03;
        $w4 = $bobotMap['C4'] ?? 0.02;

        // Nilai maksimum
        $maxC1 = max(array_column($dataSiswa, 'c1'));
        $maxC2 = max(array_column($dataSiswa, 'c2'));
        $maxC3 = max(array_column($dataSiswa, 'c3'));
        $maxC4 = max(array_column($dataSiswa, 'c4'));

        // Normalisasi & preferensi
        foreach ($dataSiswa as &$row) {
            // C1: Normal Benefit
            $row['n_c1'] = $maxC1 > 0 ? $row['c1'] / $maxC1 : 0;

            // C2: Peleburan Absensi (Nilai - Absen) / Max_Nilai
            // Semakin sedikit absen, pembilangnya semakin besar (mendekati nilai akademik aslinya)
            $row['n_c2'] = $maxC1 > 0 ? ($row['c1'] - $row['c2']) / $maxC1 : 0;

            // C3: Peleburan Ekskul (Nilai + Ekskul) / (Max_Nilai + Max_Ekskul)
            $row['n_c3'] = ($maxC1 + $maxC3) > 0 ? ($row['c1'] + $row['c3']) / ($maxC1 + $maxC3) : 0;

            // C4: Peleburan Prestasi (Nilai + Prestasi) / (Max_Nilai + Max_Prestasi)
            $row['n_c4'] = ($maxC1 + $maxC4) > 0 ? ($row['c1'] + $row['c4']) / ($maxC1 + $maxC4) : 0;

            $row['preferensi'] = ($row['n_c1'] * $w1)
                               + ($row['n_c2'] * $w2)
                               + ($row['n_c3'] * $w3)
                               + ($row['n_c4'] * $w4);
        }
        unset($row);

        // Urutkan berdasarkan preferensi DESC
        usort($dataSiswa, fn($a, $b) => $b['preferensi'] <=> $a['preferensi']);

        // Beri ranking
        foreach ($dataSiswa as $i => &$row) {
            $row['ranking'] = $i + 1;
        }
        unset($row);

        // Simpan ke database dalam transaksi
        $this->db->beginTransaction();
        try {
            $jurusan = stripos($kelas, 'MIPA') !== false ? 'MIPA' : 'IPS';

            $stmtP = $this->db->prepare("
                INSERT INTO perhitungan (
                    jenis_perhitungan, tahun_ajaran, kelas, jurusan, semester_target, id_user,
                    bobot_c1, bobot_c2, bobot_c3, bobot_c4
                )
                VALUES ('peringkat_kelas', :ta, :kelas, :jurusan, :sem, :user, :b1, :b2, :b3, :b4)
            ");
            $stmtP->execute([
                ':ta'      => $tahunAjaran,
                ':kelas'   => $kelas,
                ':jurusan' => $jurusan,
                ':sem'     => $semester,
                ':user'    => $idUser,
                ':b1'      => $w1,
                ':b2'      => $w2,
                ':b3'      => $w3,
                ':b4'      => $w4,
            ]);
            $idPerhitungan = (int) $this->db->lastInsertId();

            $stmtH = $this->db->prepare("
                INSERT INTO hasil_perhitungan
                    (id_perhitungan, id_siswa, c1_nilai_akademik, c2_absensi, c3_ekskul, c4_prestasi,
                     n_c1, n_c2, n_c3, n_c4, nilai_preferensi, rata_rata_akademik, ranking)
                VALUES
                    (:id_p, :id_s, :c1, :c2, :c3, :c4, :n1, :n2, :n3, :n4, :pref, :rata, :rank)
            ");
            foreach ($dataSiswa as $row) {
                $stmtH->execute([
                    ':id_p' => $idPerhitungan,
                    ':id_s' => $row['id_siswa'],
                    ':c1'   => $row['c1'],
                    ':c2'   => $row['c2'],
                    ':c3'   => $row['c3'],
                    ':c4'   => $row['c4'],
                    ':n1'   => $row['n_c1'],
                    ':n2'   => $row['n_c2'],
                    ':n3'   => $row['n_c3'],
                    ':n4'   => $row['n_c4'],
                    ':pref' => $row['preferensi'],
                    ':rata' => $row['rata_rata'],
                    ':rank' => $row['ranking'],
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        return [
            'id_perhitungan' => $idPerhitungan,
            'hasil'          => $dataSiswa,
        ];
    }

    // =========================================================
    // AMBIL HASIL
    // =========================================================

    public function getHasilTerbaru(string $tahunAjaran, string $kelas, int $semester): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id_perhitungan FROM perhitungan
            WHERE jenis_perhitungan = 'peringkat_kelas'
              AND tahun_ajaran = :ta AND kelas = :kelas AND semester_target = :sem
            ORDER BY tanggal_hitung DESC LIMIT 1
        ");
        $stmt->execute([':ta' => $tahunAjaran, ':kelas' => $kelas, ':sem' => $semester]);
        $row = $stmt->fetch();
        if (!$row) return null;

        return $this->getHasilByIdPerhitungan((int) $row['id_perhitungan']);
    }

    public function getHasilByIdPerhitungan(int $idPerhitungan, int $limit = 10, int $offset = 0, string $search = ''): array
    {
        $stmtBatch = $this->db->prepare("SELECT * FROM perhitungan WHERE id_perhitungan = :id");
        $stmtBatch->execute([':id' => $idPerhitungan]);
        $batch = $stmtBatch->fetch();

        // Kita hitung ulang rata-rata di SQL agar tidak terpotong oleh presisi kolom database
        $sql = "
            SELECT hp.*, s.nama, s.nisn, s.nis,
                   (hp.c1_nilai_akademik / NULLIF((
                       SELECT COUNT(*) FROM mata_pelajaran mp 
                       WHERE mp.tingkat = (CASE WHEN p.kelas LIKE 'XII%' THEN 'XII' WHEN p.kelas LIKE 'XI%' THEN 'XI' ELSE 'X' END)
                         AND mp.jurusan = (CASE WHEN p.kelas LIKE '%MIPA%' THEN 'MIPA' ELSE 'IPS' END)
                   ), 0)) as rata_rata_akurat
            FROM hasil_perhitungan hp
            JOIN siswa s ON hp.id_siswa = s.id_siswa
            JOIN perhitungan p ON p.id_perhitungan = hp.id_perhitungan
            WHERE hp.id_perhitungan = :id
              AND hp.nilai_preferensi > 0
        ";

        $params = [':id' => $idPerhitungan];
        if ($search) {
            $sql .= " AND (s.nama LIKE :q OR s.nisn LIKE :q OR s.nis LIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY hp.ranking ASC";

        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmtHasil = $this->db->prepare($sql);
            $stmtHasil->bindValue(':id', $idPerhitungan, PDO::PARAM_INT);
            if ($search) $stmtHasil->bindValue(':q', $params[':q']);
            $stmtHasil->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmtHasil->bindValue(':offset', $offset, PDO::PARAM_INT);
        } else {
            $stmtHasil = $this->db->prepare($sql);
            $stmtHasil->bindValue(':id', $idPerhitungan, PDO::PARAM_INT);
            if ($search) $stmtHasil->bindValue(':q', $params[':q']);
        }

        $stmtHasil->execute();

        return [
            'batch' => $batch,
            'rows'  => $stmtHasil->fetchAll(),
        ];
    }

    public function getHasilTotalByIdPerhitungan(int $idPerhitungan, string $search = ''): int
    {
        $sql = "SELECT COUNT(*) FROM hasil_perhitungan hp 
                JOIN siswa s ON hp.id_siswa = s.id_siswa
                WHERE hp.id_perhitungan = :id AND hp.nilai_preferensi > 0";
        $params = [':id' => $idPerhitungan];

        if ($search) {
            $sql .= " AND (s.nama LIKE :q OR s.nisn LIKE :q OR s.nis LIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getBobotAll(): array
    {
        $stmt = $this->db->query("SELECT kode_kriteria, nama_kriteria, bobot FROM kriteria ORDER BY kode_kriteria ASC");
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['kode_kriteria']] = $row;
        }
        return $result;
    }

    // =========================================================
    // RIWAYAT BATCH
    // =========================================================

    public function getRiwayatList(
        string $tahunAjaran = '',
        string $kelas = '',
        int    $semesterTarget = 0,
        int    $limit = 10,
        int    $offset = 0,
        string $jurusan = ''
    ): array {
        $where  = ["jenis_perhitungan = 'peringkat_kelas'"];
        $params = [];

        if ($tahunAjaran !== '') {
            $where[]  = 'tahun_ajaran = :ta';
            $params[':ta'] = $tahunAjaran;
        }
        if ($kelas !== '') {
            $where[]  = 'kelas = :kelas';
            $params[':kelas'] = $kelas;
        }
        if ($semesterTarget > 0) {
            $where[]  = 'semester_target = :sem';
            $params[':sem'] = $semesterTarget;
        }
        if ($jurusan !== '') {
            $where[]  = 'jurusan = :jur';
            $params[':jur'] = $jurusan;
        }

        $sql = 'SELECT p.id_perhitungan, p.tahun_ajaran, p.kelas, p.semester_target,
                       p.tanggal_hitung,
                       COUNT(hp.id_hasil) AS jumlah_siswa,
                       u.nama  AS nama_user,
                       u.role  AS role_user,
                       u.foto  AS foto_user
                FROM perhitungan p
                LEFT JOIN hasil_perhitungan hp ON hp.id_perhitungan = p.id_perhitungan
                LEFT JOIN users u ON u.id_user = p.id_user
                WHERE ' . implode(' AND ', $where) . '
                GROUP BY p.id_perhitungan
                ORDER BY p.tanggal_hitung DESC
                LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRiwayatTotal(
        string $tahunAjaran = '',
        string $kelas = '',
        int    $semesterTarget = 0,
        string $jurusan = ''
    ): int {
        $where  = ["jenis_perhitungan = 'peringkat_kelas'"];
        $params = [];

        if ($tahunAjaran !== '') { $where[] = 'tahun_ajaran = :ta';    $params[':ta']    = $tahunAjaran; }
        if ($kelas !== '')       { $where[] = 'kelas = :kelas';        $params[':kelas'] = $kelas; }
        if ($semesterTarget > 0) { $where[] = 'semester_target = :sem'; $params[':sem']   = $semesterTarget; }
        if ($jurusan !== '')     { $where[] = 'jurusan = :jur';        $params[':jur']   = $jurusan; }

        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM perhitungan WHERE ' . implode(' AND ', $where)
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function deleteBatch(int $idPerhitungan): bool
    {
        $this->db->beginTransaction();
        try {
            $this->db->prepare('DELETE FROM hasil_perhitungan WHERE id_perhitungan = ?')
                     ->execute([$idPerhitungan]);
            $this->db->prepare('DELETE FROM perhitungan WHERE id_perhitungan = ?')
                     ->execute([$idPerhitungan]);
            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
