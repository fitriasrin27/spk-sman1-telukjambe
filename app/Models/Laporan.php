<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Laporan
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT l.*, u.nama as nama_user, u.role as role_user, u.foto as foto_user, p.tahun_ajaran, p.jurusan, p.kelas as batch_kelas
                FROM laporan l
                LEFT JOIN users u ON l.id_user = u.id_user
                LEFT JOIN perhitungan p ON l.id_perhitungan = p.id_perhitungan
                WHERE l.id_laporan = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Ambil semua riwayat laporan
     */
    public function getAll(string $jenis = '', string $ta = '', int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT l.*, u.nama as nama_user, u.role as role_user, u.foto as foto_user, p.tahun_ajaran, p.jurusan, p.kelas as batch_kelas
                FROM laporan l
                LEFT JOIN users u ON l.id_user = u.id_user
                LEFT JOIN perhitungan p ON l.id_perhitungan = p.id_perhitungan
                WHERE 1=1";
        
        $params = [];
        if ($jenis) {
            $sql .= " AND l.jenis_laporan = :jenis";
            $params[':jenis'] = $jenis;
        }
        if ($ta) {
            $sql .= " AND p.tahun_ajaran LIKE :ta";
            $params[':ta'] = '%' . $ta . '%';
        }

        $sql .= " ORDER BY l.tanggal_buat DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotal(string $jenis = '', string $ta = ''): int
    {
        $sql = "SELECT COUNT(*)
                FROM laporan l
                LEFT JOIN perhitungan p ON l.id_perhitungan = p.id_perhitungan
                WHERE 1=1";
        
        $params = [];
        if ($jenis) {
            $sql .= " AND l.jenis_laporan = :jenis";
            $params[':jenis'] = $jenis;
        }
        if ($ta) {
            $sql .= " AND p.tahun_ajaran LIKE :ta";
            $params[':ta'] = '%' . $ta . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getTahunAjaranList(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT tahun_ajaran FROM perhitungan WHERE tahun_ajaran IS NOT NULL ORDER BY tahun_ajaran DESC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Hapus laporan
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT file_path FROM laporan WHERE id_laporan = :id");
        $stmt->execute([':id' => $id]);
        $path = $stmt->fetchColumn();

        if ($path && file_exists(__DIR__ . '/../../public/' . $path)) {
            unlink(__DIR__ . '/../../public/' . $path);
        }

        $stmt = $this->db->prepare("DELETE FROM laporan WHERE id_laporan = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Hapus batch laporan
     */
    public function deleteBatch(array $ids): bool
    {
        if (empty($ids)) return false;
        
        $inQuery = implode(',', array_fill(0, count($ids), '?'));
        
        // Ambil paths
        $stmt = $this->db->prepare("SELECT file_path FROM laporan WHERE id_laporan IN ($inQuery)");
        $stmt->execute($ids);
        $paths = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($paths as $path) {
            if ($path && file_exists(__DIR__ . '/../../public/' . $path)) {
                unlink(__DIR__ . '/../../public/' . $path);
            }
        }
        
        $stmtDel = $this->db->prepare("DELETE FROM laporan WHERE id_laporan IN ($inQuery)");
        return $stmtDel->execute($ids);
    }
}
