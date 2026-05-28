<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class MataPelajaran
{
    public static function orderPakemSql(string $column = 'nama_mapel', string $codeColumn = 'kode_mapel', string $idColumn = 'id_mapel'): string
    {
        $column = preg_replace('/[^a-zA-Z0-9_.]/', '', $column);
        $codeColumn = preg_replace('/[^a-zA-Z0-9_.]/', '', $codeColumn);
        $idColumn = preg_replace('/[^a-zA-Z0-9_.]/', '', $idColumn);
        $lower = "LOWER($column)";
        $code = "UPPER(TRIM($codeColumn))";

        return "
            CASE
                WHEN $code = 'TIDK' THEN 11
                WHEN $code = 'MP' THEN 12
                WHEN $lower LIKE '%agama%' OR $code = 'PADBP' THEN 1
                WHEN $lower LIKE '%pancasila%' OR $lower LIKE '%ppkn%' OR $lower LIKE '%pkn%' OR $code = 'PPDK' THEN 2
                WHEN $lower LIKE '%bahasa%indonesia%' OR $lower LIKE '%b indonesia%' OR $lower LIKE '%bindonesia%' OR $code = 'BIND' THEN 3
                WHEN ($lower LIKE '%matematika%umum%' OR $lower LIKE '%mtk%umum%' OR ($lower LIKE '%matematika%' AND $lower NOT LIKE '%peminatan%' AND $lower NOT LIKE '%mipa%' AND $lower NOT LIKE '%mp%')) OR $code = 'MU' THEN 4
                WHEN $lower LIKE '%sejarah%indonesia%' OR $lower LIKE '%sejarah indo%' OR $code = 'SI' THEN 5
                WHEN $lower LIKE '%bahasa%inggris%' OR $lower LIKE '%b inggris%' OR $lower LIKE '%binggris%' OR $code = 'BING' THEN 6
                WHEN $lower LIKE '%seni%budaya%' OR $code = 'SB' THEN 7
                WHEN $lower LIKE '%jasmani%' OR $lower LIKE '%olahraga%' OR $lower LIKE '%pjok%' OR $code = 'PJODK' THEN 8
                WHEN $lower LIKE '%prakarya%' OR $code = 'PDK' THEN 9
                WHEN $lower LIKE '%daerah%' OR $lower LIKE '%sunda%' OR $code = 'MLBD' THEN 10
                ELSE 99
            END ASC,
            CASE
                WHEN $code = 'TIDK' OR $code = 'MP'
                  OR $lower LIKE '%agama%' OR $code = 'PADBP'
                  OR $lower LIKE '%pancasila%' OR $lower LIKE '%ppkn%' OR $lower LIKE '%pkn%' OR $code = 'PPDK'
                  OR $lower LIKE '%bahasa%indonesia%' OR $lower LIKE '%b indonesia%' OR $lower LIKE '%bindonesia%' OR $code = 'BIND'
                  OR ($lower LIKE '%matematika%umum%' OR $lower LIKE '%mtk%umum%' OR ($lower LIKE '%matematika%' AND $lower NOT LIKE '%peminatan%' AND $lower NOT LIKE '%mipa%' AND $lower NOT LIKE '%mp%')) OR $code = 'MU'
                  OR $lower LIKE '%sejarah%indonesia%' OR $lower LIKE '%sejarah indo%' OR $code = 'SI'
                  OR $lower LIKE '%bahasa%inggris%' OR $lower LIKE '%b inggris%' OR $lower LIKE '%binggris%' OR $code = 'BING'
                  OR $lower LIKE '%seni%budaya%' OR $code = 'SB'
                  OR $lower LIKE '%jasmani%' OR $lower LIKE '%olahraga%' OR $lower LIKE '%pjok%' OR $code = 'PJODK'
                  OR $lower LIKE '%prakarya%' OR $code = 'PDK'
                  OR $lower LIKE '%daerah%' OR $lower LIKE '%sunda%' OR $code = 'MLBD'
                THEN $column
                ELSE NULL
            END ASC,
            CASE
                WHEN $code = 'TIDK' OR $code = 'MP'
                  OR $lower LIKE '%agama%' OR $code = 'PADBP'
                  OR $lower LIKE '%pancasila%' OR $lower LIKE '%ppkn%' OR $lower LIKE '%pkn%' OR $code = 'PPDK'
                  OR $lower LIKE '%bahasa%indonesia%' OR $lower LIKE '%b indonesia%' OR $lower LIKE '%bindonesia%' OR $code = 'BIND'
                  OR ($lower LIKE '%matematika%umum%' OR $lower LIKE '%mtk%umum%' OR ($lower LIKE '%matematika%' AND $lower NOT LIKE '%peminatan%' AND $lower NOT LIKE '%mipa%' AND $lower NOT LIKE '%mp%')) OR $code = 'MU'
                  OR $lower LIKE '%sejarah%indonesia%' OR $lower LIKE '%sejarah indo%' OR $code = 'SI'
                  OR $lower LIKE '%bahasa%inggris%' OR $lower LIKE '%b inggris%' OR $lower LIKE '%binggris%' OR $code = 'BING'
                  OR $lower LIKE '%seni%budaya%' OR $code = 'SB'
                  OR $lower LIKE '%jasmani%' OR $lower LIKE '%olahraga%' OR $lower LIKE '%pjok%' OR $code = 'PJODK'
                  OR $lower LIKE '%prakarya%' OR $code = 'PDK'
                  OR $lower LIKE '%daerah%' OR $lower LIKE '%sunda%' OR $code = 'MLBD'
                THEN NULL
                ELSE $idColumn
            END ASC
        ";
    }

    public function getAll(int $limit, int $offset, string $tingkat = '', string $jurusan = '', string $search = ''): array
    {
        $db = Database::connect();
        $query = 'SELECT * FROM mata_pelajaran WHERE 1=1';
        $params = [];

        if ($tingkat !== '') {
            $query .= ' AND tingkat = :tingkat';
            $params[':tingkat'] = $tingkat;
        }

        if ($jurusan !== '') {
            $query .= ' AND jurusan = :jurusan';
            $params[':jurusan'] = $jurusan;
        }

        if ($search !== '') {
            $query .= ' AND (kode_mapel LIKE :q OR nama_mapel LIKE :q OR tingkat LIKE :q OR jurusan LIKE :q)';
            $params[':q'] = '%' . $search . '%';
        }

        $query .= ' ORDER BY tingkat ASC, jurusan ASC, ' . self::orderPakemSql('nama_mapel') . ' LIMIT :limit OFFSET :offset';

        $stmt = $db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll(string $tingkat = '', string $jurusan = '', string $search = ''): int
    {
        $db = Database::connect();
        $query = 'SELECT COUNT(id_mapel) FROM mata_pelajaran WHERE 1=1';
        $params = [];

        if ($tingkat !== '') {
            $query .= ' AND tingkat = :tingkat';
            $params[':tingkat'] = $tingkat;
        }

        if ($jurusan !== '') {
            $query .= ' AND jurusan = :jurusan';
            $params[':jurusan'] = $jurusan;
        }

        if ($search !== '') {
            $query .= ' AND (kode_mapel LIKE :q OR nama_mapel LIKE :q OR tingkat LIKE :q OR jurusan LIKE :q)';
            $params[':q'] = '%' . $search . '%';
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function getDaftarJurusan(): array
    {
        $stmt = Database::connect()->query('SELECT DISTINCT jurusan FROM mata_pelajaran ORDER BY jurusan ASC');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Cek apakah mapel dengan kode, tingkat, dan jurusan yang sama sudah ada.
     * Pengecualian ID opsional saat edit.
     */
    public function isDuplicate(string $kode, string $tingkat, string $jurusan, int $excludeId = 0): bool
    {
        $sql = 'SELECT COUNT(id_mapel) FROM mata_pelajaran WHERE kode_mapel = :kode AND tingkat = :tingkat AND jurusan = :jurusan';
        $params = [
            ':kode'    => trim($kode),
            ':tingkat' => $tingkat,
            ':jurusan' => trim($jurusan),
        ];

        if ($excludeId > 0) {
            $sql .= ' AND id_mapel != :id';
            $params[':id'] = $excludeId;
        }

        $stmt = Database::connect()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function insert(array $data): bool
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO mata_pelajaran (kode_mapel, nama_mapel, tingkat, jurusan) 
             VALUES (:kode, :nama, :tingkat, :jurusan)'
        );
        return $stmt->execute([
            ':kode'    => trim($data['kode_mapel']),
            ':nama'    => trim($data['nama_mapel']),
            ':tingkat' => $data['tingkat'],
            ':jurusan' => trim($data['jurusan']),
        ]);
    }

    public function update(array $data): bool
    {
        $stmt = Database::connect()->prepare(
            'UPDATE mata_pelajaran SET kode_mapel = :kode, nama_mapel = :nama, tingkat = :tingkat, jurusan = :jurusan 
             WHERE id_mapel = :id'
        );
        return $stmt->execute([
            ':kode'    => trim($data['kode_mapel']),
            ':nama'    => trim($data['nama_mapel']),
            ':tingkat' => $data['tingkat'],
            ':jurusan' => trim($data['jurusan']),
            ':id'      => (int) $data['id_mapel'],
        ]);
    }

    public function findById(int $id): ?array
    {
        $db = Database::connect();
        $stmt = $db->prepare('SELECT * FROM mata_pelajaran WHERE id_mapel = :id');
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    }

    public function delete(int $id): bool
    {
        $stmt = Database::connect()->prepare('DELETE FROM mata_pelajaran WHERE id_mapel = :id');
        return $stmt->execute([':id' => $id]);
    }

    /** Helper: Render badge warna tingkat */
    public static function getBadgeTingkat(string $tingkat): string
    {
        $classes = match ($tingkat) {
            'X'   => 'badge-sem-x',
            'XI'  => 'badge-sem-xi',
            'XII' => 'badge-sem-xii',
            default => 'bg-secondary text-white'
        };
        return '<span class="badge rounded-pill ' . htmlspecialchars($classes) . '">' . htmlspecialchars($tingkat) . '</span>';
    }

    /** Helper: Render badge warna jurusan */
    public static function getBadgeJurusan(string $jurusan): string
    {
        $j = strtolower(trim($jurusan));
        $classes = 'bg-light text-dark border'; // Default
        
        if (str_contains($j, 'mipa') || str_contains($j, 'ipa')) {
            $classes = 'badge-jurusan-mipa';
        } elseif (str_contains($j, 'ips')) {
            $classes = 'badge-jurusan-ips';
        } elseif (str_contains($j, 'bahasa')) {
            $classes = 'badge-jurusan-bahasa';
        }

        return '<span class="badge rounded-pill ' . htmlspecialchars($classes) . '">' . htmlspecialchars(strtoupper($jurusan)) . '</span>';
    }
}
