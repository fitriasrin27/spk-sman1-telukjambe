<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\PerhitunganKelas;
use App\Models\RiwayatKelas;

class PerhitunganKelasController extends Controller
{
    public function __construct()
    {
        \App\Core\RoleAccess::check('perhitungan');
    }

    public function index(): void
    {
        require_login();

        $model = new PerhitunganKelas();

        $tahunAjaran   = trim($_GET['tahun_ajaran'] ?? '');
        $kelas         = trim($_GET['kelas'] ?? '');
        $semesterLabel = trim($_GET['semester'] ?? '');
        $idLihat       = (int) ($_GET['id'] ?? 0);

        $limit  = max(1, (int) ($_GET['limit'] ?? 10));
        $page   = max(1, (int) ($_GET['page']  ?? 1));
        $offset = ($page - 1) * $limit;
        $search = $_GET['q'] ?? '';

        $semesterTarget = 0;
        if ($kelas !== '' && $semesterLabel !== '') {
            $semesterTarget = RiwayatKelas::hitungSemester($kelas, $semesterLabel);
        }

        // Riwayat batch (selalu dimuat, difilter jika ada parameter)
        $total      = $model->getRiwayatTotal($tahunAjaran, $kelas, $semesterTarget);
        $totalPages = $limit > 0 ? (int) ceil($total / $limit) : 1;
        $riwayat    = $model->getRiwayatList($tahunAjaran, $kelas, $semesterTarget, $limit, $offset);

        // Hasil ranking untuk batch yang dipilih
        $hasilDipilih  = null;
        $idPerhitungan = $idLihat;
        if ($idLihat > 0) {
            $total         = $model->getHasilTotalByIdPerhitungan($idLihat, $search);
            $totalPages    = $limit > 0 ? (int) ceil($total / $limit) : 1;
            $hasilDipilih  = $model->getHasilByIdPerhitungan($idLihat, $limit, $offset, $search);
            if (!$hasilDipilih['batch']) {
                $hasilDipilih  = null;
                $idPerhitungan = 0;
            }
        }

        $this->view('perhitungan/kelas/index', [
            'title'          => 'Perhitungan Peringkat Kelas',
            'optTahunAjaran' => $model->getTahunAjaran(),
            'optKelas'       => $model->getKelas($tahunAjaran),
            'tahunAjaran'    => $tahunAjaran,
            'kelas'          => $kelas,
            'semester'       => $semesterLabel,
            'semesterTarget' => $semesterTarget,
            // riwayat list
            'riwayat'        => $riwayat,
            'total'          => $total,
            'totalPages'     => $totalPages,
            'page'           => $page,
            'limit'          => $limit,
            'offset'         => $offset,
            // hasil yang dipilih (opsional)
            'hasilDipilih'   => $hasilDipilih,
            'idPerhitungan'  => $idPerhitungan,
            'search'         => $search,
        ], 'layouts/app');
    }

    public function hitung(): void
    {
        require_login();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('perhitungan/kelas');
            return;
        }

        $tahunAjaran   = trim($_POST['tahun_ajaran'] ?? '');
        $kelas         = trim($_POST['kelas'] ?? '');
        $semesterLabel = trim($_POST['semester'] ?? '');

        if (!$tahunAjaran || !$kelas || !$semesterLabel) {
            $_SESSION['error'] = 'Tahun ajaran, kelas, dan semester wajib diisi.';
            $this->redirect('perhitungan/kelas');
            return;
        }

        $semesterTarget = RiwayatKelas::hitungSemester($kelas, $semesterLabel);
        $model          = new PerhitunganKelas();

        $idPerhitungan = 0;
        try {
            $dataSiswa = $model->getDataSiswaUntukSAW($tahunAjaran, $kelas, $semesterTarget);

            if (empty($dataSiswa)) {
                $_SESSION['error'] = "Tidak ada data siswa dengan nilai untuk kelas $kelas tahun ajaran $tahunAjaran.";
            } else {
                $idUser   = (int) (current_user()['id_user'] ?? 1);
                $hasilSAW = $model->hitungSAW($dataSiswa, $tahunAjaran, $kelas, $semesterTarget, $idUser);
                $idPerhitungan = $hasilSAW['id_perhitungan'] ?? 0;
                $jumlah   = count($hasilSAW['hasil']);
                push_notif("Peringkat kelas $kelas berhasil dihitung! $jumlah siswa telah diranking.");
            }
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'Terjadi kesalahan saat menghitung: ' . $e->getMessage();
        }

        $query = '&tahun_ajaran=' . urlencode($tahunAjaran)
               . '&kelas='        . urlencode($kelas)
               . '&semester='     . urlencode($semesterLabel);
        
        if ($idPerhitungan > 0) {
            $query .= '&id=' . $idPerhitungan;
        }

        header('Location: ' . url('perhitungan/kelas') . $query);
        exit;
    }

    public function hapusBatch(): void
    {
        require_login();

        $id            = (int) ($_POST['id_perhitungan'] ?? 0);
        $tahunAjaran   = trim($_POST['tahun_ajaran'] ?? '');
        $kelas         = trim($_POST['kelas'] ?? '');
        $semesterLabel = trim($_POST['semester'] ?? '');

        if ($id > 0) {
            $model = new PerhitunganKelas();
            if ($model->deleteBatch($id)) {
                push_notif("Riwayat perhitungan #$id berhasil dihapus.");
            } else {
                $_SESSION['error'] = "Gagal menghapus riwayat perhitungan #$id.";
            }
        }

        $query = ($tahunAjaran || $kelas || $semesterLabel)
            ? '&tahun_ajaran=' . urlencode($tahunAjaran)
              . '&kelas='      . urlencode($kelas)
              . '&semester='   . urlencode($semesterLabel)
            : '';

        header('Location: ' . url('perhitungan/kelas') . $query);
        exit;
    }

    public function deleteBatch(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('perhitungan/kelas');
            return;
        }

        $ids = $_POST['ids'] ?? [];
        if (empty($ids)) {
            $_SESSION['error'] = 'Tidak ada data yang dipilih.';
            $this->redirect('perhitungan/kelas');
            return;
        }

        $model = new PerhitunganKelas();
        $db = \App\Core\Database::connect();
        try {
            $db->beginTransaction();
            foreach ($ids as $id) {
                $model->deleteBatch((int)$id);
            }
            $db->commit();
            push_notif(count($ids) . ' riwayat perhitungan berhasil dihapus.');
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
        }
        $this->redirect('perhitungan/kelas');
    }

    public function detail(): void
    {
        require_login();

        $idPerhitungan = (int) ($_GET['id'] ?? 0);
        if ($idPerhitungan === 0) {
            $this->redirect('perhitungan/kelas');
            return;
        }

        $limit  = max(1, (int) ($_GET['limit'] ?? 10));
        $page   = max(1, (int) ($_GET['page']  ?? 1));
        $offset = ($page - 1) * $limit;

        $model = new PerhitunganKelas();
        $search     = $_GET['q'] ?? '';
        $total      = $model->getHasilTotalByIdPerhitungan($idPerhitungan, $search);
        $totalPages = $limit > 0 ? (int) ceil($total / $limit) : 1;
        $data       = $model->getHasilByIdPerhitungan($idPerhitungan, $limit, $offset, $search);
        $kriteriaRaw = $model->getBobotAll();
        $bobot = [];
        foreach ($kriteriaRaw as $kode => $k) {
            $bVal = (float)$k['bobot'];

            // Gunakan bobot histori jika kolomnya tersedia
            $colName = 'bobot_' . strtolower($kode);
            if (array_key_exists($colName, $data['batch'])) {
                $bVal = (float)$data['batch'][$colName];
            }

            $bobot[$kode] = [
                'kode_kriteria' => $kode,
                'nama_kriteria' => $k['nama_kriteria'],
                'bobot'         => $bVal
            ];
        }

        if (!$data['batch']) {
            $this->redirect('perhitungan/kelas');
            return;
        }

        $this->view('perhitungan/kelas/detail', [
            'title'      => 'Detail Perhitungan SAW — Peringkat Kelas',
            'batch'      => $data['batch'],
            'rows'       => $data['rows'],
            'bobot'      => $bobot,
            'total'      => $total,
            'totalPages' => $totalPages,
            'page'       => $page,
            'limit'      => $limit,
            'offset'     => $offset,
            'search'     => $search,
        ], 'layouts/app');
    }
}
