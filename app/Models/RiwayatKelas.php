<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class RiwayatKelas
{
    /** Label semester berdasarkan nomor (1–6) */
    public static function labelSemester(int $semester): string
    {
        $map = [
            1 => 'X — Ganjil',
            2 => 'X — Genap',
            3 => 'XI — Ganjil',
            4 => 'XI — Genap',
            5 => 'XII — Ganjil',
            6 => 'XII — Genap',
        ];
        return $map[$semester] ?? "Semester $semester";
    }

    /**
     * CSS class badge berdasarkan nomor semester.
     * Sem 1-2 = X (hijau), 3-4 = XI (biru), 5-6 = XII (ungu).
     */
    public static function badgeClass(int $semester): string
    {
        if ($semester <= 2) return 'sem-x';
        if ($semester <= 4) return 'sem-xi';
        return 'sem-xii';
    }

    /**
     * Hitung nomor semester (1–6) dari nama kelas & jenis (ganjil/genap).
     */
    public static function hitungSemester(string $kelas, string $jenisSemester): int
    {
        $kelas = strtoupper(trim($kelas));
        if (str_starts_with($kelas, 'XII'))    $tingkat = 3;
        elseif (str_starts_with($kelas, 'XI')) $tingkat = 2;
        else                                    $tingkat = 1;

        $offset = strtolower($jenisSemester) === 'genap' ? 2 : 1;
        return ($tingkat - 1) * 2 + $offset;
    }

    /**
     * Ambil semua data.
     * Tanpa filter → LEFT JOIN siswa (tampilkan juga siswa tanpa penempatan).
     * Ada filter  → INNER JOIN (hanya baris yang cocok).
     */
    public function getAll(
        string $tahunAjaran = '',
        string $kelas       = '',
        int    $semester    = 0,
        int    $offset      = 0,
        int    $limit       = 10,
        string $search      = ''
    ): array {
        $db        = Database::connect();
        
        $where  = [];
        $params = [];

        if ($tahunAjaran !== '') {
            $where[]                 = 'rk.tahun_ajaran = :tahun_ajaran';
            $params[':tahun_ajaran'] = $tahunAjaran;
        }
        if ($kelas !== '') {
            $where[]          = 'rk.kelas = :kelas';
            $params[':kelas'] = $kelas;
        }
        if ($semester > 0) {
            $where[]             = 'rk.semester = :semester';
            $params[':semester'] = $semester;
        }

        // Search
        if ($search !== '') {
            $where[] = "(s.nama LIKE :q OR s.nisn LIKE :q OR s.nis LIKE :q OR rk.kelas LIKE :q OR CAST(rk.semester AS CHAR) LIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }

        $whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // Has filter or search? Use INNER JOIN to find specific records
        $hasFilterOrSearch = $tahunAjaran !== '' || $kelas !== '' || $semester > 0 || $search !== '';
        $joinType = $hasFilterOrSearch ? 'JOIN' : 'LEFT JOIN';

        // Hitung total
        if ($hasFilterOrSearch) {
            $countSql = "SELECT COUNT(*)
                         FROM riwayat_kelas rk
                         JOIN siswa s ON s.id_siswa = rk.id_siswa
                         $whereStr";
        } else {
            $countSql = "SELECT COUNT(*)
                         FROM siswa s
                         LEFT JOIN riwayat_kelas rk ON rk.id_siswa = s.id_siswa";
        }

        $countStmt = $db->prepare($countSql);
        foreach ($params as $k => $v) $countStmt->bindValue($k, $v);
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        // Data
        $sql = "SELECT rk.id_riwayat, s.id_siswa, s.nama, s.nisn, s.nis, s.jenis_kelamin,
                       rk.tahun_ajaran, rk.kelas, rk.semester
                FROM siswa s
                $joinType riwayat_kelas rk ON rk.id_siswa = s.id_siswa
                $whereStr
                ORDER BY s.nama ASC, rk.tahun_ajaran DESC, rk.kelas ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'  => $stmt->fetchAll(),
            'total' => $total,
        ];
    }

    public function findById(int $id): array|false
    {
        $stmt = Database::connect()->prepare(
            'SELECT rk.*, s.nama, s.nisn, s.nis, s.jenis_kelamin
             FROM riwayat_kelas rk
             JOIN siswa s ON s.id_siswa = rk.id_siswa
             WHERE rk.id_riwayat = :id'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function insert(int $idSiswa, string $tahunAjaran, string $kelas, int $semester): bool
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO riwayat_kelas (id_siswa, tahun_ajaran, kelas, semester)
             VALUES (:id_siswa, :tahun_ajaran, :kelas, :semester)'
        );
        return $stmt->execute([
            ':id_siswa'     => $idSiswa,
            ':tahun_ajaran' => trim($tahunAjaran),
            ':kelas'        => trim($kelas),
            ':semester'     => $semester,
        ]);
    }

    public function update(int $id, string $tahunAjaran, string $kelas, int $semester): bool
    {
        $stmt = Database::connect()->prepare(
            'UPDATE riwayat_kelas
             SET tahun_ajaran = :tahun_ajaran, kelas = :kelas, semester = :semester
             WHERE id_riwayat = :id'
        );
        return $stmt->execute([
            ':tahun_ajaran' => trim($tahunAjaran),
            ':kelas'        => trim($kelas),
            ':semester'     => $semester,
            ':id'           => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::connect()->prepare(
            'DELETE FROM riwayat_kelas WHERE id_riwayat = :id'
        );
        return $stmt->execute([':id' => $id]);
    }

    /** Cari siswa by nama (LIKE) untuk autocomplete – maks 10 hasil. */
    public function searchSiswaByNama(string $nama): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT id_siswa, nama, nisn, nis, jenis_kelamin
             FROM siswa
             WHERE nama LIKE :nama
             ORDER BY nama ASC
             LIMIT 10'
        );
        $stmt->execute([':nama' => '%' . trim($nama) . '%']);
        return $stmt->fetchAll();
    }

    /**
     * Import satu baris riwayat kelas dari Excel.
     * Cari siswa by NISN → jika tidak ada, return 'not_found'.
     * Cek duplikat → jika ada, return 'duplicate'.
     * Berhasil → return 'inserted'.
     */
    public function importRow(string $nisn, string $tahunAjaran, string $kelas, int $semester): string
    {
        $db = Database::connect();

        // Cari siswa berdasarkan NISN
        $sel = $db->prepare('SELECT id_siswa FROM siswa WHERE nisn = :nisn');
        $sel->execute([':nisn' => trim($nisn)]);
        $idSiswa = (int) $sel->fetchColumn();

        if ($idSiswa <= 0) return 'not_found';

        // Cek duplikat
        if ($this->isDuplicate($idSiswa, $tahunAjaran, $semester)) {
            return 'duplicate';
        }

        // Insert
        $stmt = $db->prepare(
            'INSERT IGNORE INTO riwayat_kelas (id_siswa, tahun_ajaran, kelas, semester)
             VALUES (:id_siswa, :tahun_ajaran, :kelas, :semester)'
        );
        $stmt->execute([
            ':id_siswa'     => $idSiswa,
            ':tahun_ajaran' => trim($tahunAjaran),
            ':kelas'        => trim($kelas),
            ':semester'     => $semester,
        ]);

        return 'inserted';
    }

    /**
     * Cek apakah kombinasi (id_siswa, tahun_ajaran, semester) sudah ada.
     * $excludeId = id_riwayat yang sedang diedit (dikecualikan dari pengecekan).
     */
    public function isDuplicate(int $idSiswa, string $tahunAjaran, int $semester, int $excludeId = 0): bool
    {
        $stmt = Database::connect()->prepare(
            'SELECT COUNT(*) FROM riwayat_kelas
             WHERE id_siswa = :id_siswa
               AND tahun_ajaran = :tahun_ajaran
               AND semester = :semester
               AND id_riwayat != :exclude'
        );
        $stmt->execute([
            ':id_siswa'     => $idSiswa,
            ':tahun_ajaran' => $tahunAjaran,
            ':semester'     => $semester,
            ':exclude'      => $excludeId,
        ]);
        return (int) $stmt->fetchColumn() > 0;
    }

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
}
