<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Kriteria
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAll(int $offset = 0, int $limit = 10): array
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM kriteria");
        $total = $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT * FROM kriteria ORDER BY id_kriteria ASC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return [
            'total' => $total,
            'data' => $data
        ];
    }

    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM kriteria WHERE id_kriteria = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function findByKode(string $kode)
    {
        $stmt = $this->db->prepare("SELECT * FROM kriteria WHERE kode_kriteria = :kode");
        $stmt->execute([':kode' => $kode]);
        return $stmt->fetch();
    }

    public function getNextKode(): string
    {
        $stmt = $this->db->query("SELECT kode_kriteria FROM kriteria ORDER BY id_kriteria DESC LIMIT 1");
        $lastKode = $stmt->fetchColumn();

        if ($lastKode) {
            $num = (int) substr($lastKode, 1);
            return 'C' . ($num + 1);
        }
        return 'C1';
    }

    public function getTotalBobot(): float
    {
        $stmt = $this->db->query("SELECT SUM(bobot) FROM kriteria");
        return (float) $stmt->fetchColumn();
    }

    public function insert(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO kriteria (kode_kriteria, nama_kriteria, atribut, bobot) VALUES (:kode, :nama, :atribut, :bobot)");
        $stmt->execute([
            ':kode' => $data['kode_kriteria'],
            ':nama' => $data['nama_kriteria'],
            ':atribut' => $data['atribut'],
            ':bobot' => $data['bobot']
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE kriteria SET kode_kriteria = :kode, nama_kriteria = :nama, atribut = :atribut, bobot = :bobot WHERE id_kriteria = :id");
        return $stmt->execute([
            ':kode' => $data['kode_kriteria'],
            ':nama' => $data['nama_kriteria'],
            ':atribut' => $data['atribut'],
            ':bobot' => $data['bobot'],
            ':id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM kriteria WHERE id_kriteria = :id");
        return $stmt->execute([':id' => $id]);
    }
}
