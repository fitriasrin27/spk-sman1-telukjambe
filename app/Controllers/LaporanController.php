<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Laporan;

class LaporanController extends Controller
{
    private Laporan $laporanModel;

    public function __construct()
    {
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
            $this->laporanModel->delete($id);
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
            push_notif(count($ids) . ' laporan berhasil dihapus.');
        } else {
            $_SESSION['error'] = 'Tidak ada data yang dipilih.';
        }
        
        header('Location: ' . url('laporan'));
    }
}