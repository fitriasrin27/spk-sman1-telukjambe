<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Laporan;

class LaporanController extends Controller
{
    private Laporan $laporanModel;

    public function __construct()
    {
        \App\Core\RoleAccess::check('laporan');
        $this->laporanModel = new Laporan();
    }

    public function index(): void
    {
        require_login();

        $jenis = $_GET['jenis_laporan'] ?? '';
        $ta    = $_GET['tahun_ajaran'] ?? '';

        $limit = (int)($_GET['limit'] ?? 10);
        $page  = (int)($_GET['page']  ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $dataLaporan = $this->laporanModel->getAll($jenis, $ta, $limit, $offset);
        $total       = $this->laporanModel->getTotal($jenis, $ta);
        $totalPages  = ceil($total / $limit);

        $optTahunAjaran = $this->laporanModel->getTahunAjaranList();

        $this->view('laporan/index', [
            'title'          => 'Laporan',
            'active'         => 'laporan',
            'laporan'        => $dataLaporan,
            'jenisLaporan'   => $jenis,
            'tahunAjaran'    => $ta,
            'optTahunAjaran' => $optTahunAjaran,
            'total'          => $total,
            'totalPages'     => $totalPages,
            'page'           => $page,
            'limit'          => $limit,
            'offset'         => $offset,
        ], 'layouts/app');
    }

    public function detail(): void
    {
        require_login();
        $id = (int)($_GET['id'] ?? 0);
        
        $laporan = $this->laporanModel->getById($id);
        
        if (!$laporan) {
            set_flash_message('Laporan tidak ditemukan.', 'danger');
            header('Location: ' . url('laporan'));
            exit;
        }

        $filename = basename($laporan['file_path']);
        log_activity("Melihat detail laporan: {$filename}", 'laporan');

        $this->view('laporan/detail', [
            'title'   => 'Laporan',
            'active'  => 'laporan',
            'laporan' => $laporan
        ], 'layouts/app');
    }

    public function delete(): void
    {
        require_login();
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id > 0) {
            $l = $this->laporanModel->getById($id);
            $file = $l ? basename($l['file_path']) : "ID {$id}";
            $this->laporanModel->delete($id);
            log_activity("Menghapus file laporan: {$file}", 'laporan');
            push_notif('Laporan berhasil dihapus.');
        }

        header('Location: ' . url('laporan'));
    }

    public function deleteBatch(): void
    {
        require_login();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ids']) && is_array($_POST['ids'])) {
            $ids = array_map('intval', $_POST['ids']);
            $this->laporanModel->deleteBatch($ids);
            log_activity("Menghapus massal " . count($ids) . " berkas laporan", 'laporan');
            push_notif(count($ids) . ' laporan berhasil dihapus.');
        } else {
            $_SESSION['error'] = 'Tidak ada data yang dipilih.';
        }
        
        header('Location: ' . url('laporan'));
    }

    public function download(): void
    {
        require_login();
        $id = (int)($_GET['id'] ?? 0);
        
        $l = $this->laporanModel->getById($id);
        if (!$l) {
            $_SESSION['error'] = 'Laporan tidak ditemukan.';
            header('Location: ' . url('laporan'));
            exit;
        }

        $filename = basename($l['file_path']);
        log_activity("Mengunduh berkas laporan: {$filename}", 'laporan');
        
        push_notif("Berkas laporan {$filename} berhasil diunduh.");

        header('Location: ' . asset($l['file_path']));
        exit;
    }
}