<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\PerhitunganKelas;
use App\Models\PerhitunganEligible;
use App\Core\Database;
use Dompdf\Dompdf;
use Dompdf\Options;

class HasilController extends Controller
{
    private PerhitunganKelas $kelasModel;
    private PerhitunganEligible $eligibleModel;

    public function __construct()
    {
        \App\Core\RoleAccess::check('hasil');
        $this->kelasModel = new PerhitunganKelas();
        $this->eligibleModel = new PerhitunganEligible();
    }

    public function kelas(): void
    {
        require_login();

        $tahunAjaran = $_GET['tahun_ajaran'] ?? '';
        $kelas       = $_GET['kelas'] ?? '';
        $semesterRaw = $_GET['semester'] ?? '';
        $jurusan     = $_GET['jurusan'] ?? '';

        // Pagination for Riwayat List
        $limit = (int)($_GET['limit'] ?? 10);
        $page  = (int)($_GET['page']  ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        // Logika konversi Ganjil/Genap ke Semester Target (1-6)
        $semesterTarget = 0;
        if ($semesterRaw === 'ganjil' || $semesterRaw === 'genap') {
            $isGenap = ($semesterRaw === 'genap');
            if (strpos($kelas, 'XII') === 0) {
                $semesterTarget = $isGenap ? 6 : 5;
            } elseif (strpos($kelas, 'XI') === 0) {
                $semesterTarget = $isGenap ? 4 : 3;
            } elseif (strpos($kelas, 'X') === 0) {
                $semesterTarget = $isGenap ? 2 : 1;
            }
        }

        // Ambil daftar riwayat berdasarkan filter
        $riwayat = $this->kelasModel->getRiwayatList($tahunAjaran, $kelas, $semesterTarget, $limit, $offset, $jurusan);
        $total   = $this->kelasModel->getRiwayatTotal($tahunAjaran, $kelas, $semesterTarget, $jurusan);
        $totalPages = ceil($total / $limit);

        $this->view('hasil/kelas/index', [
            'title'          => 'Hasil Akhir Peringkat Kelas',
            'subActive'      => 'hasil_kelas',
            'tahunAjaran'    => $tahunAjaran,
            'kelas'          => $kelas,
            'semester'       => $semesterRaw, // Kirim raw 'ganjil'/'genap' untuk filter UI
            'jurusan'        => $jurusan,
            'riwayat'        => $riwayat,
            'total'          => $total,
            'totalPages'     => $totalPages,
            'page'           => $page,
            'limit'          => $limit,
            'offset'         => $offset,
            'optTahunAjaran' => $this->kelasModel->getTahunAjaran(),
            'optKelas'       => $this->kelasModel->getKelas($tahunAjaran)
        ], 'layouts/app');
    }

    public function kelasLihat(): void
    {
        require_login();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . url('hasil/kelas'));
            exit;
        }

        // Pagination for Result Rows
        $limit  = (int)($_GET['limit'] ?? 10);
        $page   = (int)($_GET['page']  ?? 1);
        $search = $_GET['q'] ?? '';
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $data = $this->kelasModel->getHasilByIdPerhitungan($id, $limit, $offset, $search);
        if (!$data['batch']) {
            header('Location: ' . url('hasil/kelas'));
            exit;
        }

        $totalResults = $this->kelasModel->getHasilTotalByIdPerhitungan($id, $search);
        $totalPages   = ceil($totalResults / $limit);

        $this->view('hasil/kelas/lihat', [
            'title'        => 'Lihat Hasil Peringkat Kelas',
            'subActive'    => 'hasil_kelas',
            'id'           => $id,
            'batch'        => $data['batch'],
            'hasil'        => $data['rows'],
            'limit'        => $limit,
            'page'         => $page,
            'total'        => $totalResults,
            'totalPages'   => $totalPages,
            'from'         => ($totalResults > 0) ? $offset + 1 : 0,
            'to'           => min($offset + $limit, $totalResults),
            'offset'       => $offset,
            'search'       => $search
        ], 'layouts/app');
    }

    public function eligible(): void
    {
        require_login();

        $tahunAjaran = $_GET['tahun_ajaran'] ?? '';
        $jurusan     = $_GET['jurusan'] ?? '';

        // Pagination for Riwayat List
        $limit = (int)($_GET['limit'] ?? 10);
        $page  = (int)($_GET['page']  ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        // Ambil daftar riwayat berdasarkan filter
        $riwayat = $this->eligibleModel->getRiwayat($tahunAjaran, $jurusan, $limit, $offset);
        $total   = $this->eligibleModel->getRiwayatTotal($tahunAjaran, $jurusan);
        $totalPages = ceil($total / $limit);

        $this->view('hasil/eligible/index', [
            'title'          => 'Hasil Akhir Peringkat Eligible',
            'subActive'      => 'hasil_eligible',
            'tahunAjaran'    => $tahunAjaran,
            'jurusan'        => $jurusan,
            'riwayat'        => $riwayat,
            'total'          => $total,
            'totalPages'     => $totalPages,
            'page'           => $page,
            'limit'          => $limit,
            'offset'         => $offset,
            'optTahunAjaran' => $this->eligibleModel->getTahunAjaran(),
        ], 'layouts/app');
    }

    public function eligibleLihat(): void
    {
        require_login();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . url('hasil/eligible'));
            exit;
        }

        // Pagination for Result Rows
        $limit  = (int)($_GET['limit'] ?? 10);
        $page   = (int)($_GET['page']  ?? 1);
        $search = $_GET['q'] ?? '';
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $limit;

        $data = $this->eligibleModel->getHasilByIdPerhitungan($id, $limit, $offset, $search);
        if (!$data['riwayat']) {
            header('Location: ' . url('hasil/eligible'));
            exit;
        }

        $totalResults = $this->eligibleModel->getHasilTotalByIdPerhitungan($id, $search);
        $totalPages   = ceil($totalResults / $limit);

        $this->view('hasil/eligible/lihat', [
            'title'        => 'Lihat Hasil Peringkat Eligible',
            'subActive'    => 'hasil_eligible',
            'id'           => $id,
            'batch'        => $data['riwayat'],
            'hasil'        => $data['hasil'],
            'limit'        => $limit,
            'page'         => $page,
            'total'        => $totalResults,
            'totalPages'   => $totalPages,
            'from'         => ($totalResults > 0) ? $offset + 1 : 0,
            'to'           => min($offset + $limit, $totalResults),
            'offset'       => $offset,
            'search'       => $search
        ], 'layouts/app');
    }


    public function generatePdf(): void
    {
        header('Content-Type: application/json');
        try {
            require_login();
            date_default_timezone_set('Asia/Jakarta');
            
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) {
                throw new \Exception('Data request tidak valid.');
            }

            $idPerhitungan = isset($data['id_perhitungan']) ? (int)$data['id_perhitungan'] : 0;
            $jenis         = $data['jenis'] ?? ''; // 'kelas' atau 'eligible'
            $orientasi     = $data['orientasi'] ?? 'portrait';
            $komponen      = $data['komponen'] ?? [];
            $ttd           = $data['ttd'] ?? [];

            // 1. Ambil Data
            $hasil = [];
            $batch = [];
            if ($jenis === 'kelas') {
                $res = $this->kelasModel->getHasilByIdPerhitungan($idPerhitungan, 0); 
                $hasil = $res['rows'];
                $batch = $res['batch'];
            } else {
                $res = $this->eligibleModel->getHasilByIdPerhitungan($idPerhitungan, 0);
                $hasil = $res['hasil'];
                $batch = $res['riwayat'];
            }

            if (!$batch) {
                throw new \Exception('Data batch perhitungan tidak ditemukan.');
            }

            // 2. Persiapan Folder
            $uploadDir = __DIR__ . '/../../public/uploads/laporan/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            // 3. Nama File
            $jenisLabel = ($jenis === 'kelas') ? 'Peringkat-Kelas' : 'Peringkat-Eligible';
            $subLabel   = ($jenis === 'kelas') ? $batch['kelas'] : $batch['jurusan'];
            $taClean    = str_replace('/', '-', $batch['tahun_ajaran']);
            $subClean   = str_replace(' ', '-', $subLabel);
            $timestamp  = date('d-m-Y_H-i-s');
            
            $filename = "Laporan_{$jenisLabel}_{$taClean}_{$subClean}_{$timestamp}.pdf";
            $filePath = 'uploads/laporan/' . $filename;

            // 4. Render HTML
            ob_start();
            $this->view('hasil/pdf-template', [
                'title'          => $filename,
                'judulDokumen'   => ($jenis === 'kelas' ? 'Hasil Peringkat Kelas' : 'Hasil Seleksi Siswa Eligible SNBP'),
                'jenis'          => $jenis,
                'batch'          => $batch,
                'hasil'          => $hasil,
                'komponen'       => $komponen,
                'ttd'            => $ttd,
                'semesterLabel'  => ($jenis === 'kelas' ? (in_array($batch['semester_target'] ?? 0, [1,3,5]) ? 'Ganjil' : 'Genap') : ''),
                'logoJabar'      => 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(__DIR__ . '/../../public/assets/img/logo-pemprov-jabar.svg')),
                'logoSekolah'    => 'data:image/png;base64,' . base64_encode(file_get_contents(__DIR__ . '/../../public/assets/img/logo-sman1-nobg.png')),
                'bulanId'        => ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
            ]);
            $html = ob_get_clean();

            // 5. Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', $orientasi);
            $dompdf->render();
            $dompdf->add_info('Title', $filename);

            // 6. Simpan File
            file_put_contents($uploadDir . $filename, $dompdf->output());

            // 7. Simpan Histori ke DB
            $db = Database::connect();
            $stmt = $db->prepare("
                INSERT INTO laporan (id_perhitungan, id_user, jenis_laporan, orientasi, komponen_laporan, file_path, tanggal_buat)
                VALUES (:id_p, :id_u, :jenis, :ori, :komp, :path, :tgl)
            ");
            
            $userId = $_SESSION['user']['id_user'] ?? null;
            if (!$userId) throw new \Exception('Sesi user tidak ditemukan. Silakan login ulang.');

            $stmt->execute([
                ':id_p'  => $idPerhitungan > 0 ? $idPerhitungan : null,
                ':id_u'  => $userId,
                ':jenis' => $jenis, 
                ':ori'   => $orientasi,
                ':komp'  => json_encode($komponen),
                ':path'  => $filePath,
                ':tgl'   => date('Y-m-d H:i:s')
            ]);

            $jenisText = ($jenis === 'kelas') ? 'Peringkat Kelas' : 'Peringkat Eligible';
            log_activity("Membuat laporan baru: {$filename} (Jenis: {$jenisText})", 'laporan');

            echo json_encode([
                'success'      => true,
                'download_url' => asset($filePath),
                'filename'     => $filename
            ]);
        } catch (\Throwable $e) {
            // Log error ke file untuk debugging (Hanya untuk dev)
            $logDir = __DIR__ . '/../../storage/logs/';
            if (!is_dir($logDir)) mkdir($logDir, 0777, true);
            file_put_contents($logDir . 'error_debug.log', date('[Y-m-d H:i:s] ') . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n", FILE_APPEND);
            
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }
}