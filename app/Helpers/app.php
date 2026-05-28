<?php

function getCurrentPath(): string
{
    if (isset($_GET['url'])) {
        return trim((string) $_GET['url'], '/');
    }

    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($uri, PHP_URL_PATH);

    return trim((string) $path, '/');
}

function resolveMenu(): array
{
    $path = getCurrentPath();

    $map = [
        'dashboard' => ['dashboard', null],

        'siswa' => ['siswa', 'identitas'],
        'nilai' => ['siswa', 'nilai'],
        'riwayat-kelas' => ['siswa', 'riwayat-kelas'],
        'mata-pelajaran' => ['siswa', 'mata-pelajaran'],

        'kriteria' => ['kriteria', 'kriteria'],
        'konversi' => ['kriteria', 'konversi'],

        'perhitungan/kelas' => ['perhitungan', 'hitung_kelas'],
        'perhitungan/detail-kelas' => ['perhitungan', 'hitung_kelas'],
        'perhitungan/eligible' => ['perhitungan', 'hitung_eligible'],
        'perhitungan/eligible-hasil' => ['perhitungan', 'hitung_eligible'],
        'perhitungan/eligible-detail' => ['perhitungan', 'hitung_eligible'],

        'hasil/kelas' => ['perhitungan', 'hasil_kelas'],
        'hasil/kelas-lihat' => ['perhitungan', 'hasil_kelas'],
        'hasil/eligible' => ['perhitungan', 'hasil_eligible'],
        'hasil/eligible-lihat' => ['perhitungan', 'hasil_eligible'],
        'laporan' => ['laporan', null],
        'akun' => ['akun', null],
        'profile' => ['profile', 'profile'],
        'log' => ['log', null],
    ];

    return $map[$path] ?? [null, null];
}

function time_elapsed($datetime)
{
    $tz = new \DateTimeZone('Asia/Jakarta');
    $now = new \DateTime('now', $tz);
    $then = new \DateTime($datetime, $tz);

    $diff = $now->getTimestamp() - $then->getTimestamp();

    if ($diff < 0) {
        $diff = 0;
    }

    if ($diff < 60) return "baru saja";
    if ($diff < 3600) return floor($diff/60) . " menit yang lalu";
    if ($diff < 86400) return floor($diff/3600) . " jam yang lalu";

    return floor($diff/86400) . " hari yang lalu";
}

/**
 * Tambah notifikasi ke sesi.
 * - Disimpan di $_SESSION['notif'] (max 7, FIFO).
 * - $_SESSION['notif_new'] diset untuk trigger toast sekali pakai.
 */
function push_notif(string $message): void
{
    if (!isset($_SESSION['notif'])) {
        $_SESSION['notif'] = [];
    }

    $entry = [
        'message' => $message,
        'time'    => date('Y-m-d H:i:s'),
        'ts'      => time(),               // Unix timestamp untuk format jam di JS (timezone lokal browser)
    ];

    $_SESSION['notif'][] = $entry;

    // Potong agar maksimal 10 (hapus yang paling lama / index awal)
    if (count($_SESSION['notif']) > 10) {
        $_SESSION['notif'] = array_slice($_SESSION['notif'], -10);
    }

    // Flag untuk toast popup (akan dikonsumsi sekali di layout)
    $_SESSION['notif_new'] = $entry;
}

/**
 * Catat aktivitas ke database secara otomatis.
 *
 * @param string $activity Deskripsi tindakan
 * @param string $module Nama modul (auth, siswa, nilai, perhitungan, kriteria, laporan, akun)
 */
function log_activity(string $activity, string $module): void
{
    try {
        $logModel = new \App\Models\ActivityLog();
        $logModel->insertLog($activity, $module);
    } catch (\Throwable $e) {
        error_log("Gagal mencatat log aktivitas: " . $e->getMessage());
    }
}