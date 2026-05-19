<?php

namespace App\Controllers;

use App\Core\Controller;

class NotifController extends Controller
{
    /**
     * AJAX: hapus satu notif dari sesi berdasarkan index.
     * POST body: { index: 0 }
     */
    public function dismiss(): void
    {
        header('Content-Type: application/json');

        $body  = json_decode(file_get_contents('php://input'), true);
        $index = isset($body['index']) ? (int) $body['index'] : -1;

        if ($index >= 0 && isset($_SESSION['notif'][$index])) {
            array_splice($_SESSION['notif'], $index, 1);
            echo json_encode(['ok' => true, 'remaining' => count($_SESSION['notif'])]);
        } else {
            http_response_code(400);
            echo json_encode(['ok' => false]);
        }
    }

    /**
     * AJAX: hapus semua notifikasi dari sesi.
     */
    public function clearAll(): void
    {
        header('Content-Type: application/json');
        $_SESSION['notif'] = [];
        echo json_encode(['ok' => true]);
    }

    /**
     * AJAX: Tambah notifikasi baru ke sesi.
     * POST body: { message: "..." }
     */
    public function add(): void
    {
        header('Content-Type: application/json');
        $body = json_decode(file_get_contents('php://input'), true);
        $msg  = $body['message'] ?? '';

        if ($msg) {
            $newNotif = [
                'message' => $msg,
                'time'    => date('Y-m-d H:i:s'),
                'ts'      => time()
            ];
            
            if (!isset($_SESSION['notif'])) $_SESSION['notif'] = [];
            $_SESSION['notif'][] = $newNotif;
            // Set flag untuk tampilkan toast otomatis jika diinginkan (opsional)
            // $_SESSION['notif_new'] = $newNotif; 

            echo json_encode(['ok' => true]);
        } else {
            http_response_code(400);
            echo json_encode(['ok' => false]);
        }
    }
}
