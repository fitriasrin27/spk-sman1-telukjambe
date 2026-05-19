<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Konversi
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAll(int $offset = 0, int $limit = 10, int $id_kriteria = 0): array
    {
        $where = "";
        $params = [
            ':limit' => $limit,
            ':offset' => $offset
        ];

        if ($id_kriteria > 0) {
            $where = " WHERE k.id_kriteria = :id_kriteria ";
            $params[':id_kriteria'] = $id_kriteria;
        }

        $stmt = $this->db->prepare("
            SELECT k.*, kr.nama_kriteria, kr.kode_kriteria 
            FROM konversi_nilai k
            JOIN kriteria kr ON k.id_kriteria = kr.id_kriteria
            $where
            ORDER BY kr.kode_kriteria ASC, k.nilai_konversi DESC
            LIMIT :limit OFFSET :offset
        ");
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        
        $stmt->execute();
        $data = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) FROM konversi_nilai k";
        if ($id_kriteria > 0) {
            $countSql .= " WHERE k.id_kriteria = :id_kriteria";
            $stmtCount = $this->db->prepare($countSql);
            $stmtCount->execute([':id_kriteria' => $id_kriteria]);
        } else {
            $stmtCount = $this->db->query($countSql);
        }
        
        $total = $stmtCount->fetchColumn();

        return [
            'data' => $data,
            'total' => (int) $total
        ];
    }

    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM konversi_nilai WHERE id_konversi = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function insert(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO konversi_nilai (id_kriteria, nilai_asli, nilai_konversi) VALUES (:id_kriteria, :nilai_asli, :nilai_konversi)");
        $stmt->execute([
            ':id_kriteria' => $data['id_kriteria'],
            ':nilai_asli' => $data['nilai_asli'],
            ':nilai_konversi' => $data['nilai_konversi']
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE konversi_nilai SET id_kriteria = :id_kriteria, nilai_asli = :nilai_asli, nilai_konversi = :nilai_konversi WHERE id_konversi = :id");
        return $stmt->execute([
            ':id' => $id,
            ':id_kriteria' => $data['id_kriteria'],
            ':nilai_asli' => $data['nilai_asli'],
            ':nilai_konversi' => $data['nilai_konversi']
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM konversi_nilai WHERE id_konversi = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function checkDuplicate(int $id_kriteria, string $nilai_asli, int $exclude_id = 0): bool
    {
        $sql = "SELECT COUNT(*) FROM konversi_nilai WHERE id_kriteria = :id_kriteria AND nilai_asli = :nilai_asli";
        $params = [
            ':id_kriteria' => $id_kriteria,
            ':nilai_asli' => $nilai_asli
        ];

        if ($exclude_id > 0) {
            $sql .= " AND id_konversi != :exclude_id";
            $params[':exclude_id'] = $exclude_id;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
