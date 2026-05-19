<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Siswa
{
    public function getAll(
        string $tahunAjaran = '',
        string $kelas = '',
        int $offset = 0,
        int $limit = 10,
        string $search = ''
    ): array {
        $db     = Database::connect();
        $where  = ['1=1'];
        $params = [];

        $join   = '';
        $select = 'SELECT s.*';

        // Filter Tahun Ajaran / Kelas (join ke riwayat_kelas)
        if ($tahunAjaran !== '' || $kelas !== '') {
            $join   = 'JOIN riwayat_kelas rk ON rk.id_siswa = s.id_siswa';
            $select = 'SELECT DISTINCT s.*';

            if ($tahunAjaran !== '') {
                $where[]                  = 'rk.tahun_ajaran = :tahun_ajaran';
                $params[':tahun_ajaran']  = $tahunAjaran;
            }
            if ($kelas !== '') {
                $where[]        = 'rk.kelas = :kelas';
                $params[':kelas'] = $kelas;
            }
        }

        // Search
        if ($search !== '') {
            $where[] = '(s.nama LIKE :q OR s.nisn LIKE :q OR s.nis LIKE :q)';
            $params[':q'] = '%' . $search . '%';
        }

        $whereStr = implode(' AND ', $where);

        // Hitung total
        $countSelect = ($tahunAjaran !== '' || $kelas !== '' || $search !== '')
            ? 'SELECT COUNT(DISTINCT s.id_siswa)'
            : 'SELECT COUNT(*)';

        $countSql  = "$countSelect FROM siswa s $join WHERE $whereStr";
        $countStmt = $db->prepare($countSql);
        foreach ($params as $key => $val) {
            $countStmt->bindValue($key, $val);
        }
        $countStmt->execute();
        $total = (int) $countStmt->fetchColumn();

        // Ambil data dengan limit & offset
        $sql  = "$select FROM siswa s $join WHERE $whereStr ORDER BY s.nama ASC LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'  => $stmt->fetchAll(),
            'total' => $total,
        ];
    }

    public function findById(int $id): array|false
    {
        $stmt = Database::connect()->prepare('SELECT * FROM siswa WHERE id_siswa = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
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

    public function insert(array $data): int
    {
        $db   = Database::connect();
        $stmt = $db->prepare(
            'INSERT INTO siswa (nama, nisn, nis, jenis_kelamin) VALUES (:nama, :nisn, :nis, :jenis_kelamin)'
        );
        $stmt->execute([
            ':nama'          => trim($data['nama']),
            ':nisn'          => trim($data['nisn']),
            ':nis'           => trim($data['nis']),
            ':jenis_kelamin' => $data['jenis_kelamin'],
        ]);
        return (int) $db->lastInsertId();
    }

    public function insertRiwayatKelas(int $idSiswa, string $tahunAjaran, string $kelas, int $semester): bool
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

    /**
     * Menghitung nomor semester (1–6) berdasarkan tingkat kelas dan jenis semester.
     * Tingkat X  → semester 1 (ganjil) / 2 (genap)
     * Tingkat XI → semester 3 (ganjil) / 4 (genap)
     * Tingkat XII→ semester 5 (ganjil) / 6 (genap)
     */
    public function hitungSemester(string $kelas, string $jenisSemester): int
    {
        $kelas = strtoupper(trim($kelas));
        if (str_starts_with($kelas, 'XII'))    $tingkat = 3;
        elseif (str_starts_with($kelas, 'XI')) $tingkat = 2;
        else                                    $tingkat = 1;

        $offset = strtolower($jenisSemester) === 'genap' ? 2 : 1;
        return ($tingkat - 1) * 2 + $offset;
    }

    public function update(array $data): bool
    {
        $stmt = Database::connect()->prepare(
            'UPDATE siswa SET nama = :nama, nisn = :nisn, nis = :nis, jenis_kelamin = :jenis_kelamin
             WHERE id_siswa = :id'
        );
        return $stmt->execute([
            ':nama'          => trim($data['nama']),
            ':nisn'          => trim($data['nisn']),
            ':nis'           => trim($data['nis']),
            ':jenis_kelamin' => $data['jenis_kelamin'],
            ':id'            => (int) $data['id_siswa'],
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::connect()->prepare('DELETE FROM siswa WHERE id_siswa = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Digunakan saat import Excel.
     * INSERT IGNORE pada siswa (lewati jika NISN sudah ada),
     * lalu tetap coba INSERT IGNORE ke riwayat_kelas.
     * Return: true jika siswa baru, false jika sudah ada.
     */
    public function importRow(array $data): bool
    {
        $db = Database::connect();

        // INSERT IGNORE — lewati baris jika NISN/NIS sudah ada
        $stmt = $db->prepare(
            'INSERT IGNORE INTO siswa (nama, nisn, nis, jenis_kelamin)
             VALUES (:nama, :nisn, :nis, :jk)'
        );
        $stmt->execute([
            ':nama' => trim($data['nama']),
            ':nisn' => trim($data['nisn']),
            ':nis'  => trim($data['nis']),
            ':jk'   => $data['jenis_kelamin'],
        ]);
        $isNew = (int) $db->lastInsertId() > 0;

        // Cari id_siswa (baru atau lama) berdasarkan NISN
        $sel = $db->prepare('SELECT id_siswa FROM siswa WHERE nisn = :nisn');
        $sel->execute([':nisn' => trim($data['nisn'])]);
        $idSiswa = (int) $sel->fetchColumn();

        if ($idSiswa <= 0) return false;

        // INSERT IGNORE ke riwayat_kelas (abaikan jika kombinasi id_siswa+tahun_ajaran+semester sudah ada)
        $rk = $db->prepare(
            'INSERT IGNORE INTO riwayat_kelas (id_siswa, tahun_ajaran, kelas, semester)
             VALUES (:id_siswa, :tahun_ajaran, :kelas, :semester)'
        );
        $rk->execute([
            ':id_siswa'     => $idSiswa,
            ':tahun_ajaran' => trim($data['tahun_ajaran']),
            ':kelas'        => trim($data['kelas']),
            ':semester'     => (int) $data['semester'],
        ]);

        return $isNew;
    }

    public function isNisnExists(string $nisn, int $excludeId = 0): bool
    {
        $stmt = Database::connect()->prepare(
            'SELECT COUNT(*) FROM siswa WHERE nisn = :nisn AND id_siswa != :id'
        );
        $stmt->execute([':nisn' => $nisn, ':id' => $excludeId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function isNisExists(string $nis, int $excludeId = 0): bool
    {
        $stmt = Database::connect()->prepare(
            'SELECT COUNT(*) FROM siswa WHERE nis = :nis AND id_siswa != :id'
        );
        $stmt->execute([':nis' => $nis, ':id' => $excludeId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
