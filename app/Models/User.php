<?php

namespace App\Models;

use App\Core\Database;

class User
{
    public function findByUsername(string $username): ?array
    {
        $statement = Database::connect()->prepare(
            'SELECT * FROM users WHERE username = :username LIMIT 1'
        );

        $statement->execute(['username' => $username]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function updateLastLogin(int $idUser): void
    {
        $statement = Database::connect()->prepare(
            'UPDATE users SET last_login = NOW() WHERE id_user = :id_user'
        );

        $statement->execute(['id_user' => $idUser]);
    }

    public function getAll(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $db = Database::connect();
        $cols = $db->query("SHOW COLUMNS FROM users LIKE 'posisi'")->fetch();
        $selectPosisi = $cols ? "posisi" : "'' as posisi";
        
        // Cek apakah kolom 'password_plain' ada
        $colsPlain = $db->query("SHOW COLUMNS FROM users LIKE 'password_plain'")->fetch();
        $selectPlain = $colsPlain ? "password_plain" : "'' as password_plain";

        // Cek apakah kolom 'foto' ada
        $colsFoto = $db->query("SHOW COLUMNS FROM users LIKE 'foto'")->fetch();
        $selectFoto = $colsFoto ? "foto" : "'' as foto";

        $sql = "SELECT id_user, nama, $selectPosisi, username, $selectPlain, $selectFoto, role, last_login, created_at FROM users WHERE 1=1";
        $params = [];

        if ($search) {
            $sql .= " AND (nama LIKE :search OR username LIKE :search OR role LIKE :search";
            if ($cols) {
                $sql .= " OR posisi LIKE :search";
            }
            $sql .= ")";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTotal(string $search = ''): int
    {
        $db = Database::connect();
        $sql = "SELECT COUNT(*) FROM users WHERE 1=1";
        $params = [];

        if ($search) {
            $sql .= " AND (nama LIKE :search OR username LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function isUsernameExists(string $username, int $excludeId = 0): bool
    {
        $db = Database::connect();
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $params = [':username' => $username];

        if ($excludeId > 0) {
            $sql .= " AND id_user != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function insert(array $data): int
    {
        $db = Database::connect();
        
        // Cek kolom yang tersedia
        $cols = $db->query("SHOW COLUMNS FROM users")->fetchAll(\PDO::FETCH_COLUMN);
        $hasPosisi = in_array('posisi', $cols);
        $hasPlain = in_array('password_plain', $cols);

        $fields = ["nama", "username", "password", "role", "created_at"];
        $values = [":nama", ":username", ":password", ":role", "NOW()"];
        $params = [
            ':nama'     => $data['nama'],
            ':username' => $data['username'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role'     => $data['role']
        ];

        if ($hasPosisi) {
            $fields[] = "posisi";
            $values[] = ":posisi";
            $params[':posisi'] = $data['posisi'] ?? '';
        }

        if ($hasPlain) {
            $fields[] = "password_plain";
            $values[] = ":password_plain";
            $params[':password_plain'] = $data['password'];
        }

        $sql = "INSERT INTO users (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return (int) $db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $db = Database::connect();
        
        // Cek kolom yang tersedia
        $cols = $db->query("SHOW COLUMNS FROM users")->fetchAll(\PDO::FETCH_COLUMN);
        $hasPosisi = in_array('posisi', $cols);
        $hasPlain = in_array('password_plain', $cols);

        $fields = ["nama = :nama", "username = :username", "role = :role"];
        $params = [
            ':nama'     => $data['nama'],
            ':username' => $data['username'],
            ':role'     => $data['role'],
            ':id'       => $id
        ];

        if ($hasPosisi) {
            $fields[] = "posisi = :posisi";
            $params[':posisi'] = $data['posisi'] ?? '';
        }

        if (!empty($data['password'])) {
            $fields[] = "password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            
            if ($hasPlain) {
                $fields[] = "password_plain = :password_plain";
                $params[':password_plain'] = $data['password'];
            }
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id_user = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $db = Database::connect();
        $stmt = $db->prepare("DELETE FROM users WHERE id_user = :id");
        return $stmt->execute([':id' => $id]);
    }
}
