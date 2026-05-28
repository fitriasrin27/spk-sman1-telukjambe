<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        \App\Core\RoleAccess::check('log');
    }

    /**
     * Tampilan utama halaman Log Aktivitas.
     */
    public function index(): void
    {
        require_login();

        $logModel = new ActivityLog();
        
        // Jalankan pembersihan otomatis data log di atas 6 bulan
        $logModel->autoCleanup();

        // Parameter filter & pencarian
        $search    = $_GET['q'] ?? '';
        $role      = $_GET['role'] ?? '';
        $modul     = $_GET['modul'] ?? '';
        $rentang   = $_GET['rentang'] ?? 'all';
        $startDate = $_GET['start_date'] ?? '';
        $endDate   = $_GET['end_date'] ?? '';

        // Parameter paginasi
        $limit  = max(1, (int)($_GET['limit'] ?? 10));
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $filters = [
            'q'          => $search,
            'role'       => $role,
            'modul'      => $modul,
            'rentang'    => $rentang,
            'start_date' => $startDate,
            'end_date'   => $endDate
        ];

        // Ambil data
        $logs       = $logModel->getLogs($filters, $limit, $offset);
        $logs       = $this->formatAktivitas($logs);
        $total      = $logModel->getTotal($filters);
        $totalPages = (int) ceil($total / $limit);
        $metrics    = $logModel->getMetricsToday();

        // Tampilkan halaman view
        $this->view('log/index', [
            'title'      => 'Log Aktivitas',
            'logs'       => $logs,
            'total'      => $total,
            'limit'      => $limit,
            'page'       => $page,
            'totalPages' => $totalPages,
            'search'     => $search,
            'role'       => $role,
            'modul'      => $modul,
            'rentang'    => $rentang,
            'startDate'  => $startDate,
            'endDate'    => $endDate,
            'metrics'    => $metrics
        ], 'layouts/app');
    }

    /**
     * Endpoint API JSON untuk AJAX Polling Real-time dan navigasi filter/pagination.
     */
    public function apiFetch(): void
    {
        require_login();

        header('Content-Type: application/json');

        $logModel = new ActivityLog();
        
        // Baca lastId secara opsional (hanya untuk polling log baru)
        $lastId = isset($_GET['last_id']) ? (int) $_GET['last_id'] : null;

        // Filter aktif
        $search    = $_GET['q'] ?? '';
        $role      = $_GET['role'] ?? '';
        $modul     = $_GET['modul'] ?? '';
        $rentang   = $_GET['rentang'] ?? 'all';
        $startDate = $_GET['start_date'] ?? '';
        $endDate   = $_GET['end_date'] ?? '';

        // Parameter paginasi
        $limit  = max(1, (int)($_GET['limit'] ?? 10));
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $filters = [
            'q'          => $search,
            'role'       => $role,
            'modul'      => $modul,
            'rentang'    => $rentang,
            'start_date' => $startDate,
            'end_date'   => $endDate
        ];

        $metrics = $logModel->getMetricsToday();
        
        if ($lastId !== null && $lastId > 0) {
            // Polling logs baru saja (untuk live update)
            $logs = $logModel->getLatestLogsSince($lastId, $filters);
            $total = 0;
            $totalPages = 0;
        } else {
            // Pemuatan data table ter-filter / terpaginasi
            $logs       = $logModel->getLogs($filters, $limit, $offset);
            $total      = $logModel->getTotal($filters);
            $totalPages = (int) ceil($total / $limit);
        }

        $logs = $this->formatAktivitas($logs);

        // Tambahkan format waktu dan visual pendukung sebelum dikirim ke Client
        foreach ($logs as &$log) {
            $log['time_elapsed']   = time_elapsed($log['created_at']);
            $log['formatted_date'] = date('d/m/Y H:i:s', strtotime($log['created_at']));
            $log['nama_display']   = $log['nama'] ?: ($log['username_fallback'] ? '@' . $log['username_fallback'] : 'Tamu/Sistem');
        }

        echo json_encode([
            'success'    => true,
            'logs'       => $logs,
            'total'      => $total,
            'limit'      => $limit,
            'page'       => $page,
            'totalPages' => $totalPages,
            'metrics'    => $metrics
        ]);
        exit;
    }

    /**
     * Menerjemahkan nama file laporan dalam teks aktivitas menjadi link detail laporan secara aman.
     */
    private function formatAktivitas(array $logs): array
    {
        $pathsToLookup = [];
        foreach ($logs as $log) {
            if (preg_match('/((?:Laporan|Leger-Nilai)_[a-zA-Z0-9_\-\.]+\.pdf)/i', $log['aktivitas'], $matches)) {
                $filename = $matches[1];
                if (str_starts_with(strtolower($filename), 'leger-nilai')) {
                    $pathsToLookup[] = 'uploads/leger/' . $filename;
                } else {
                    $pathsToLookup[] = 'uploads/laporan/' . $filename;
                }
            }
        }
        
        $reportMap = [];
        if (!empty($pathsToLookup)) {
            try {
                $db = \App\Core\Database::connect();
                $inPlaceholder = implode(',', array_fill(0, count($pathsToLookup), '?'));
                $stmt = $db->prepare("SELECT id_laporan, file_path FROM laporan WHERE file_path IN ($inPlaceholder)");
                $stmt->execute($pathsToLookup);
                $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $filename = basename($row['file_path']);
                    $reportMap[$filename] = $row['id_laporan'];
                }
            } catch (\Throwable $e) {
                error_log("Gagal batch query report map: " . $e->getMessage());
            }
        }

        foreach ($logs as &$log) {
            $aktivitas = $log['aktivitas'];
            if (preg_match('/((?:Laporan|Leger-Nilai)_[a-zA-Z0-9_\-\.]+\.pdf)/i', $aktivitas, $matches)) {
                $filename = $matches[1];
                if (isset($reportMap[$filename])) {
                    $id = $reportMap[$filename];
                    $link = '<a href="' . url('laporan/detail') . '&id=' . $id . '" class="text-primary fw-semibold text-decoration-none hover-underline" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i>' . $filename . '</a>';
                    $parts = explode($filename, $aktivitas, 2);
                    $log['aktivitas'] = e($parts[0]) . $link . e($parts[1]);
                    continue;
                }
            }
            $log['aktivitas'] = e($aktivitas);
        }
        
        return $logs;
    }
}
