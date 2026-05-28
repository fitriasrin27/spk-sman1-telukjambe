<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\PerhitunganEligible;

class PerhitunganEligibleController extends Controller
{
    private $model;

    public function __construct()
    {
        \App\Core\RoleAccess::check('perhitungan');
        $this->model = new PerhitunganEligible();
    }

    public function index(): void
    {
        require_login();

        $tahunAjaran = trim($_GET['tahun_ajaran'] ?? '');
        $jurusan     = trim($_GET['jurusan'] ?? '');
        $idLihat     = (int) ($_GET['id'] ?? 0);
        $search      = $_GET['q'] ?? '';

        // Pagination parameters
        $limit       = (int)($_GET['limit'] ?? 10);
        $page        = (int)($_GET['page'] ?? 1);
        if ($page < 1) $page = 1;
        $offset      = ($page - 1) * $limit;

        // Opsi untuk filter
        $optTahunAjaran = $this->model->getTahunAjaran();
        
        // Hasil jika dipilih (Prioritas pagination untuk hasil)
        $hasilDipilih = null;
        if ($idLihat > 0) {
            $total        = $this->model->getHasilTotalByIdPerhitungan($idLihat, $search);
            $totalPages   = ceil($total / $limit);
            $hasilDipilih = $this->model->getHasilByIdPerhitungan($idLihat, $limit, $offset, $search);
            
            // Masih butuh data riwayat dasar (tanpa pagination) untuk filter jika diperlukan,
            // tapi biasanya kita tampilkan riwayat list di halaman yang sama.
            // Di modul Kelas, riwayat list tetap dipaginasi jika ID tidak ada.
            $riwayat = $this->model->getRiwayat($tahunAjaran, $jurusan, 10, 0); 
        } else {
            // Daftar Riwayat (Pagination untuk riwayat)
            $riwayat      = $this->model->getRiwayat($tahunAjaran, $jurusan, $limit, $offset);
            $total        = $this->model->countRiwayat($tahunAjaran, $jurusan);
            $totalPages   = ceil($total / $limit);
        }

        // Info pendaftar & kuota (untuk card seleksi)
        $totalSiswaJurusan = 0;
        $jumlahPendaftar   = 0;
        $kuotaEligible     = 0;
        $daftarKelas       = [];
        if ($tahunAjaran && $jurusan) {
            $totalSiswaJurusan = $this->model->countTotalSiswaJurusan($tahunAjaran, $jurusan);
            $jumlahPendaftar   = $this->model->countPendaftar($tahunAjaran, $jurusan);
            $kuotaEligible     = (int)round($totalSiswaJurusan * 0.40);
            $daftarKelas       = $this->model->getDaftarKelasXII($tahunAjaran, $jurusan);
        }

        $this->view('perhitungan/eligible/index', [
            'title'               => 'Perhitungan Peringkat Eligible (SNBP)',
            'riwayat'             => $riwayat,
            'tahunAjaran'         => $tahunAjaran,
            'jurusan'             => $jurusan,
            'optTahunAjaran'      => $optTahunAjaran,
            'hasilDipilih'        => $hasilDipilih,
            'idPerhitungan'       => $idLihat,
            // Kuota & pendaftar
            'totalSiswaJurusan'   => $totalSiswaJurusan,
            'jumlahPendaftar'     => $jumlahPendaftar,
            'kuotaEligible'       => $kuotaEligible,
            'daftarKelas'         => $daftarKelas,
            // Pagination Data
            'total'               => $total,
            'totalPages'          => $totalPages,
            'page'                => $page,
            'limit'               => $limit,
            'offset'              => $offset,
            'search'              => $search
        ], 'layouts/app');
    }

    /**
     * AJAX: Ambil daftar siswa kelas XII per kelas untuk panel seleksi
     */
    public function getSiswa(): void
    {
        require_login();
        header('Content-Type: application/json');

        $tahunAjaran = trim($_GET['tahun_ajaran'] ?? '');
        $jurusan     = trim($_GET['jurusan'] ?? '');
        $kelas       = trim($_GET['kelas'] ?? '');

        if (!$tahunAjaran || !$jurusan) {
            echo json_encode(['error' => 'Parameter tidak lengkap']);
            return;
        }

        $siswa = $this->model->getSiswaKelasXII($tahunAjaran, $jurusan, $kelas);
        echo json_encode($siswa);
    }

    public function hitung(): void
    {
        require_login();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('perhitungan/eligible');
            return;
        }

        $tahunAjaran  = $_POST['tahun_ajaran'] ?? '';
        $jurusan      = $_POST['jurusan'] ?? '';
        $idRiwayatList = $_POST['id_riwayat'] ?? [];

        if ($tahunAjaran === '' || $jurusan === '') {
            $_SESSION['error'] = 'Pilih Tahun Ajaran dan Jurusan.';
            $this->redirect('perhitungan/eligible');
            return;
        }

        if (empty($idRiwayatList)) {
            $_SESSION['error'] = 'Pilih minimal satu siswa pendaftar SNBP terlebih dahulu.';
            $this->redirect('perhitungan/eligible' . '&tahun_ajaran=' . urlencode($tahunAjaran) . '&jurusan=' . urlencode($jurusan));
            return;
        }

        $idPerhitungan = 0;
        try {
            // hitungSAW() sekarang handle semuanya:
            // 1. Resolve siswa dari idRiwayatList
            // 2. Hitung SAW
            // 3. Insert ke perhitungan
            // 4. Log ke peserta_eligible (per perhitungan)
            // 5. Insert ke hasil_perhitungan
            $idPerhitungan = $this->model->hitungSAW($tahunAjaran, $jurusan, $idRiwayatList);
            if ($idPerhitungan > 0) {
                log_activity("Menjalankan kalkulasi Peringkat Eligible SNBP SAW: Angkatan {$tahunAjaran} Jurusan {$jurusan}", 'perhitungan');
                push_notif("Perhitungan Eligible angkatan $tahunAjaran $jurusan berhasil dilakukan.");
            }
        } catch (\Throwable $e) {
            error_log('[EligibleController] ' . $e->getMessage());
            $_SESSION['error'] = 'Gagal hitung SAW: ' . $e->getMessage();
        }

        $query = '&tahun_ajaran=' . urlencode($tahunAjaran) . '&jurusan=' . urlencode($jurusan);
        if ($idPerhitungan > 0) {
            $query .= '&id=' . $idPerhitungan;
        }


        header('Location: ' . url('perhitungan/eligible') . $query);
        exit;
    }

    public function detail(): void
    {
        require_login();
        $id = (int)($_GET['id'] ?? 0);
        $search = $_GET['q'] ?? '';
        
        $limit  = (int)($_GET['limit'] ?? 10);
        $page   = (int)($_GET['page']  ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        // Ambil total hasil untuk pagination
        $totalResults = $this->model->getHasilTotalByIdPerhitungan($id, $search);
        $totalPages   = ceil($totalResults / $limit);

        // Ambil data (menggunakan pagination)
        $data = $this->model->getHasilByIdPerhitungan($id, $limit, $offset, $search);

        if (empty($data)) {
            $_SESSION['error'] = 'Data tidak ditemukan.';
            $this->redirect('perhitungan/eligible');
            return;
        }

        // Ambil kriteria saat ini untuk nama & metadata
        $kriteriaRaw = $this->model->getKriteria();
        $bobot = [];
        foreach ($kriteriaRaw as $k) {
            $kode = $k['kode_kriteria'];
            $bVal = $k['bobot'];

            // Gunakan bobot histori jika kolomnya tersedia
            $colName = 'bobot_' . strtolower($kode);
            if (array_key_exists($colName, $data['riwayat'])) {
                $bVal = (float)$data['riwayat'][$colName];
            }

            $bobot[$kode] = [
                'kode_kriteria' => $kode,
                'nama_kriteria' => $k['nama_kriteria'],
                'bobot'         => $bVal
            ];
        }

        // Hitung kuota eligible dari total siswa jurusan
        $totalSiswaJurusan = $this->model->countTotalSiswaJurusan(
            $data['riwayat']['tahun_ajaran'],
            $data['riwayat']['jurusan']
        );
        $kuotaEligible = (int)round($totalSiswaJurusan * 0.40);

        $this->view('perhitungan/eligible/detail', [
            'title'             => 'Detail Perhitungan SAW Eligible',
            'riwayat'           => $data['riwayat'],
            'batch'             => $data['riwayat'],
            'rows'              => $data['hasil'],
            'bobot'             => $bobot,
            'kuotaEligible'     => $kuotaEligible,
            'totalSiswaJurusan' => $totalSiswaJurusan,
            'limit'      => $limit,
            'page'       => $page,
            'total'      => $totalResults,
            'totalPages' => $totalPages,
            'from'       => ($totalResults > 0) ? $offset + 1 : 0,
            'to'         => min($offset + $limit, $totalResults),
            'search'     => $search
        ], 'layouts/app');
    }

    public function delete(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('perhitungan/eligible');
            return;
        }
        $id = (int)($_POST['id'] ?? 0);
        if ($id === 0) {
            $_SESSION['error'] = 'ID Perhitungan tidak valid.';
            $this->redirect('perhitungan/eligible');
            return;
        }
        $db = \App\Core\Database::connect();
        try {
            $db->beginTransaction();
            $db->prepare("DELETE FROM hasil_perhitungan WHERE id_perhitungan = :id")->execute([':id' => $id]);
            $db->prepare("DELETE FROM peserta_eligible WHERE id_perhitungan = :id")->execute([':id' => $id]);
            $db->prepare("DELETE FROM perhitungan WHERE id_perhitungan = :id AND jenis_perhitungan = 'peringkat_eligible'")->execute([':id' => $id]);
            $db->commit();
            log_activity("Menghapus riwayat perhitungan Peringkat Eligible ID: #{$id}", 'perhitungan');
            push_notif('Riwayat perhitungan eligible berhasil dihapus.');
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Gagal menghapus riwayat: ' . $e->getMessage();
        }

        $this->redirect('perhitungan/eligible');
    }

    public function deleteBatch(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('perhitungan/eligible');
            return;
        }

        $ids = $_POST['ids'] ?? [];
        if (empty($ids)) {
            $_SESSION['error'] = 'Tidak ada data yang dipilih.';
            $this->redirect('perhitungan/eligible');
            return;
        }

        $db = \App\Core\Database::connect();
        try {
            $db->beginTransaction();
            
            // Format placeholders
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            
            $db->prepare("DELETE FROM hasil_perhitungan WHERE id_perhitungan IN ($placeholders)")->execute($ids);
            $db->prepare("DELETE FROM peserta_eligible WHERE id_perhitungan IN ($placeholders)")->execute($ids);
            $db->prepare("DELETE FROM perhitungan WHERE id_perhitungan IN ($placeholders) AND jenis_perhitungan = 'peringkat_eligible'")->execute($ids);
            
            $db->commit();
            log_activity("Menghapus massal " . count($ids) . " riwayat perhitungan Peringkat Eligible", 'perhitungan');
            push_notif(count($ids) . ' riwayat perhitungan eligible berhasil dihapus.');
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Gagal menghapus riwayat: ' . $e->getMessage();
        }

        $this->redirect('perhitungan/eligible');
    }
}
