<?php

namespace App\Models;

use App\Core\Database;

class ActivityLog
{
    public function __construct()
    {
        $this->checkAndCreateTable();
    }

    /**
     * Memeriksa dan membuat tabel activity_logs secara otomatis jika belum ada.
     */
    private function checkAndCreateTable(): void
    {
        try {
            $db = Database::connect();
            $q = $db->query("SHOW TABLES LIKE 'activity_logs'")->fetch();
            if (!$q) {
                $sql = "CREATE TABLE IF NOT EXISTS activity_logs (
                    id_log INT AUTO_INCREMENT PRIMARY KEY,
                    id_user INT NULL,
                    username_fallback VARCHAR(50) NULL,
                    aktivitas VARCHAR(255) NOT NULL,
                    modul VARCHAR(50) NOT NULL,
                    ip_address VARCHAR(45) NOT NULL,
                    user_agent VARCHAR(255) NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                $db->exec($sql);
            }
        } catch (\Throwable $e) {
            error_log("Gagal inisialisasi tabel activity_logs: " . $e->getMessage());
        }
    }

    /**
     * Memasukkan log baru ke database.
     */
    public function insertLog(string $activity, string $module): void
    {
        try {
            $db = Database::connect();
            $user = current_user();

            $idUser = $user ? ($user['id_user'] ?? null) : null;
            $usernameFallback = $user ? ($user['username'] ?? null) : null;

            // Ekstrak username jika aksi berupa percobaan login gagal
            if ($module === 'auth' && stripos($activity, 'Gagal login sebagai') !== false) {
                preg_match('/Gagal login sebagai\s+[\'"]?([a-zA-Z0-9_\-\.]+)/i', $activity, $matches);
                if (!empty($matches[1])) {
                    $usernameFallback = $matches[1];
                }
            }

            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            // Bersihkan format IPv6 local loopback jika ada
            if ($ip === '::1') {
                $ip = '127.0.0.1';
            }
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            $stmt = $db->prepare("INSERT INTO activity_logs (id_user, username_fallback, aktivitas, modul, ip_address, user_agent) VALUES (:id_user, :username_fallback, :aktivitas, :modul, :ip_address, :user_agent)");
            $stmt->execute([
                ':id_user'           => $idUser,
                ':username_fallback' => $usernameFallback,
                ':aktivitas'         => $activity,
                ':modul'             => $module,
                ':ip_address'        => $ip,
                ':user_agent'        => substr($ua, 0, 255)
            ]);
        } catch (\Throwable $e) {
            error_log("Database Error di insertLog: " . $e->getMessage());
        }
    }

    /**
     * Membangun filter WHERE clause untuk query log.
     */
    private function buildWhereClause(array $filters, array &$params): string
    {
        $sql = " WHERE 1=1";

        if (!empty($filters['q'])) {
            $sql .= " AND (l.aktivitas LIKE :q OR l.username_fallback LIKE :q OR u.nama LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        if (!empty($filters['role'])) {
            $sql .= " AND u.role = :role";
            $params[':role'] = $filters['role'];
        }

        if (!empty($filters['modul'])) {
            $sql .= " AND l.modul = :modul";
            $params[':modul'] = $filters['modul'];
        }

        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(l.created_at) >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(l.created_at) <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        if (!empty($filters['rentang']) && $filters['rentang'] !== 'all') {
            $rentang = $filters['rentang'];
            if ($rentang === 'today') {
                $sql .= " AND DATE(l.created_at) = CURDATE()";
            } elseif ($rentang === 'week') {
                $sql .= " AND l.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            } elseif ($rentang === 'month') {
                $sql .= " AND l.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            }
        }

        return $sql;
    }


    /**
     * Mengambil daftar log terfilter dan terpaginasi.
     */
    public function getLogs(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        try {
            $db = Database::connect();
            $params = [];
            $where = $this->buildWhereClause($filters, $params);

            $sql = "SELECT l.*, u.nama, u.role as user_role, u.foto 
                    FROM activity_logs l 
                    LEFT JOIN users u ON l.id_user = u.id_user" 
                    . $where . 
                    " ORDER BY l.id_log DESC LIMIT :limit OFFSET :offset";

            $stmt = $db->prepare($sql);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            error_log("Database Error di getLogs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Menghitung total log terfilter untuk pagination.
     */
    public function getTotal(array $filters = []): int
    {
        try {
            $db = Database::connect();
            $params = [];
            $where = $this->buildWhereClause($filters, $params);

            $sql = "SELECT COUNT(*) FROM activity_logs l LEFT JOIN users u ON l.id_user = u.id_user" . $where;
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            return (int) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            error_log("Database Error di getTotal: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mengambil log terbaru semenjak ID tertentu (untuk real-time AJAX Polling).
     */
    public function getLatestLogsSince(int $lastId, array $filters = []): array
    {
        try {
            $db = Database::connect();
            $params = [':last_id' => $lastId];
            $where = $this->buildWhereClause($filters, $params);

            // Batasi WHERE l.id_log > :last_id
            $where = str_replace("WHERE 1=1", "WHERE l.id_log > :last_id", $where);

            $sql = "SELECT l.*, u.nama, u.role as user_role, u.foto 
                    FROM activity_logs l 
                    LEFT JOIN users u ON l.id_user = u.id_user" 
                    . $where . 
                    " ORDER BY l.id_log ASC"; // ASC agar urutan baris baru masuknya berurutan di JS

            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            error_log("Database Error di getLatestLogsSince: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Mendapatkan nilai 5 metrics card untuk hari ini.
     */
    public function getMetricsToday(): array
    {
        try {
            $db = Database::connect();

            // 1. Total Aktivitas Hari Ini
            $q1 = $db->query("SELECT COUNT(*) FROM activity_logs WHERE DATE(created_at) = CURDATE()")->fetchColumn();

            // 2. Pengguna Aktif Hari Ini
            $q2 = $db->query("SELECT COUNT(DISTINCT id_user) FROM activity_logs WHERE DATE(created_at) = CURDATE() AND id_user IS NOT NULL")->fetchColumn();

            // 3. Total Perhitungan Hari Ini
            $q3 = $db->query("SELECT COUNT(*) FROM activity_logs WHERE modul = 'perhitungan' AND DATE(created_at) = CURDATE()")->fetchColumn();

            // 4. Laporan Dibuat Hari Ini
            $q4 = $db->query("SELECT COUNT(*) FROM activity_logs WHERE modul = 'laporan' AND aktivitas LIKE 'Membuat laporan%' AND DATE(created_at) = CURDATE()")->fetchColumn();

            // 5. Laporan Diunduh Hari Ini
            $q5 = $db->query("SELECT COUNT(*) FROM activity_logs WHERE modul = 'laporan' AND aktivitas LIKE 'Mengunduh%' AND DATE(created_at) = CURDATE()")->fetchColumn();

            // Ambil laporan terbaru dibuat hari ini (untuk subtext link)
            $latestCreated = $db->query("SELECT id_laporan, file_path FROM laporan WHERE DATE(tanggal_buat) = CURDATE() ORDER BY id_laporan DESC LIMIT 1")->fetch();
            $latestCreatedName = $latestCreated ? basename($latestCreated['file_path']) : null;
            $latestCreatedUrl = $latestCreated ? url('laporan/detail') . '&id=' . $latestCreated['id_laporan'] : null;

            // Ambil laporan terbaru diunduh hari ini
            $latestDownloadLog = $db->query("SELECT aktivitas FROM activity_logs WHERE modul = 'laporan' AND aktivitas LIKE 'Mengunduh%' AND DATE(created_at) = CURDATE() ORDER BY id_log DESC LIMIT 1")->fetchColumn();
            
            $latestDownloadedName = null;
            $latestDownloadedUrl = null;
            if ($latestDownloadLog) {
                // Ekstrak nama file setelah titik dua
                if (preg_match('/Mengunduh berkas laporan:\s*(.+)/i', $latestDownloadLog, $matches)) {
                    $latestDownloadedName = trim($matches[1]);
                    // Cari id_laporan dari database
                    $stmtL = $db->prepare("SELECT id_laporan, file_path FROM laporan WHERE file_path LIKE :filename LIMIT 1");
                    $stmtL->execute([':filename' => '%' . $latestDownloadedName]);
                    $lap = $stmtL->fetch();
                    if ($lap) {
                        $latestDownloadedUrl = url('laporan/download') . '&id=' . $lap['id_laporan'];
                    } else {
                        // Jika tidak ada di tabel laporan, link langsung ke asset
                        if (str_starts_with(strtolower($latestDownloadedName), 'leger-nilai')) {
                            $latestDownloadedUrl = asset('uploads/leger/' . $latestDownloadedName);
                        } else {
                            $latestDownloadedUrl = asset('uploads/laporan/' . $latestDownloadedName);
                        }
                    }
                }
            }

            return [
                'total_activities'      => (int) $q1,
                'active_users'          => (int) $q2,
                'total_calculations'    => (int) $q3,
                'total_reports_created' => (int) $q4,
                'total_reports_downloaded'=> (int) $q5,
                'latest_created_name'   => $latestCreatedName,
                'latest_created_url'    => $latestCreatedUrl,
                'latest_downloaded_name'=> $latestDownloadedName,
                'latest_downloaded_url' => $latestDownloadedUrl,
            ];
        } catch (\Throwable $e) {
            error_log("Database Error di getMetricsToday: " . $e->getMessage());
            return [
                'total_activities'      => 0,
                'active_users'          => 0,
                'total_calculations'    => 0,
                'total_reports_created' => 0,
                'total_reports_downloaded'=> 0,
                'latest_created_name'   => null,
                'latest_created_url'    => null,
                'latest_downloaded_name'=> null,
                'latest_downloaded_url' => null,
            ];
        }
    }


    /**
     * Menghapus log otomatis.
     * Strategi ganda:
     *  1. Jika total baris > 25.000, hapus baris terlama sampai tersisa 25.000 (FIFO) — selalu aktif.
     *  2. Hanya di bulan Juni: hapus log yang berusia > 1 tahun (selaras tutup tahun ajaran).
     */
    public function autoCleanup(): void
    {
        try {
            $db = Database::connect();

            // 1. FIFO Cap — selalu aktif sebagai safety net
            $total = (int) $db->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn();
            $cap   = 25000;

            if ($total > $cap) {
                $surplus = $total - $cap;
                $db->prepare(
                    "DELETE FROM activity_logs
                     ORDER BY id_log ASC
                     LIMIT :surplus"
                )->execute([':surplus' => $surplus]);
            }

            // 2. Tutup tahun ajaran — hanya jalan di bulan Juni
            if ((int) date('n') === 6) {
                $db->exec("DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)");
            }
        } catch (\Throwable $e) {
            error_log("Database Error di autoCleanup: " . $e->getMessage());
        }
    }
}
