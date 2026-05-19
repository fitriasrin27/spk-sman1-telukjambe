<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $config = require __DIR__ . '/../Config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";

        try {
            self::$connection = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            return self::$connection;
        } catch (PDOException $exception) {
            die('Koneksi database gagal: ' . $exception->getMessage());
        }
    }
}

