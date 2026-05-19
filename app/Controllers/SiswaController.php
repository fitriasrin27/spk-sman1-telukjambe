<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Siswa;

class SiswaController extends Controller
{
    public function __construct()
    {
        \App\Core\RoleAccess::check('siswa');
    }

    public function index(): void
    {
        require_login();

        $model = new Siswa();

        $tahunAjaran = $_GET['tahun_ajaran'] ?? '';
        $kelas       = $_GET['kelas'] ?? '';
        $search      = $_GET['q'] ?? '';
        $limit       = max(1, (int) ($_GET['limit'] ?? 10));
        $page        = max(1, (int) ($_GET['page'] ?? 1));
        $offset      = ($page - 1) * $limit;

        $result     = $model->getAll($tahunAjaran, $kelas, $offset, $limit, $search);
        $totalPages = (int) ceil($result['total'] / max(1, $limit));

        // Ambil error dari sesi lalu hapus
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->view('siswa/index', [
            'title'              => 'Data Siswa',
            'siswa'              => $result['data'],
            'total'              => $result['total'],
            'page'               => $page,
            'limit'              => $limit,
            'totalPages'         => $totalPages,
            'offset'             => $offset,
            'tahunAjaran'        => $tahunAjaran,
            'kelas'              => $kelas,
            'search'             => $search,
            'daftarTahunAjaran'  => $model->getTahunAjaran(),
            'daftarKelas'        => $model->getKelas($tahunAjaran),
            'error'              => $error,
        ], 'layouts/app');
    }

    public function store(): void
    {
        require_login();

        $model  = new Siswa();
        $errors = [];

        if (empty(trim($_POST['nama'] ?? '')))         $errors[] = 'Nama siswa tidak boleh kosong.';
        if (empty(trim($_POST['nisn'] ?? '')))         $errors[] = 'NISN tidak boleh kosong.';
        if (empty(trim($_POST['nis'] ?? '')))          $errors[] = 'NIS tidak boleh kosong.';
        if (empty($_POST['jenis_kelamin'] ?? ''))      $errors[] = 'Jenis kelamin harus dipilih.';
        if (empty(trim($_POST['tahun_ajaran'] ?? ''))) $errors[] = 'Tahun ajaran tidak boleh kosong.';
        if (empty(trim($_POST['kelas'] ?? '')))        $errors[] = 'Kelas tidak boleh kosong.';
        if (empty($_POST['semester_jenis'] ?? ''))     $errors[] = 'Semester harus dipilih.';

        if (empty($errors) && $model->isNisnExists(trim($_POST['nisn']))) {
            $errors[] = 'NISN sudah digunakan oleh siswa lain.';
        }
        if (empty($errors) && $model->isNisExists(trim($_POST['nis']))) {
            $errors[] = 'NIS sudah digunakan oleh siswa lain.';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('siswa');
            return;
        }

        $idSiswa  = $model->insert($_POST);
        $semester = $model->hitungSemester(trim($_POST['kelas']), $_POST['semester_jenis']);
        $model->insertRiwayatKelas($idSiswa, trim($_POST['tahun_ajaran']), trim($_POST['kelas']), $semester);

        push_notif('Data siswa berhasil ditambahkan.');

        $this->redirect('siswa');
    }

    public function update(): void
    {
        require_login();

        $model  = new Siswa();
        $id     = (int) ($_POST['id_siswa'] ?? 0);
        $errors = [];

        if (empty(trim($_POST['nama'] ?? '')))          $errors[] = 'Nama siswa tidak boleh kosong.';
        if (empty(trim($_POST['nisn'] ?? '')))          $errors[] = 'NISN tidak boleh kosong.';
        if (empty(trim($_POST['nis'] ?? '')))           $errors[] = 'NIS tidak boleh kosong.';
        if (empty($_POST['jenis_kelamin'] ?? ''))       $errors[] = 'Jenis kelamin harus dipilih.';

        if (empty($errors) && $model->isNisnExists(trim($_POST['nisn']), $id)) {
            $errors[] = 'NISN sudah digunakan oleh siswa lain.';
        }
        if (empty($errors) && $model->isNisExists(trim($_POST['nis']), $id)) {
            $errors[] = 'NIS sudah digunakan oleh siswa lain.';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('siswa');
            return;
        }

        $model->update($_POST);

        push_notif('Data siswa berhasil diperbarui.');

        $this->redirect('siswa');
    }

    public function delete(): void
    {
        require_login();

        $id = $_GET['id'] ?? null;

        if ($id) {
            $model = new \App\Models\Siswa();
            $model->delete($id);

            push_notif('Data siswa berhasil dihapus.');
        }

        $this->redirect('siswa');
    }

    public function deleteBatch(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('siswa');
            return;
        }

        $ids = $_POST['ids'] ?? [];
        if (empty($ids)) {
            $_SESSION['error'] = 'Tidak ada data yang dipilih.';
            $this->redirect('siswa');
            return;
        }

        $model = new Siswa();
        $db = \App\Core\Database::connect();
        try {
            $db->beginTransaction();
            foreach ($ids as $id) {
                $model->delete((int)$id);
            }
            $db->commit();
            push_notif(count($ids) . ' data siswa berhasil dihapus.');
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
        }

        $this->redirect('siswa');
    }

    public function import(): void
    {
        require_login();

        if (empty($_FILES['excel_file']['tmp_name'])) {
            $_SESSION['error'] = 'Pilih file Excel terlebih dahulu.';
            $this->redirect('siswa');
            return;
        }

        $file = $_FILES['excel_file'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['xlsx', 'xls'])) {
            $_SESSION['error'] = 'Format tidak valid. Gunakan .xlsx atau .xls';
            $this->redirect('siswa');
            return;
        }

        $autoload = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($autoload)) {
            $_SESSION['error'] = 'Library PhpSpreadsheet belum terinstall. Jalankan: composer install';
            $this->redirect('siswa');
            return;
        }

        require_once $autoload;

        try {
            $reader      = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file['tmp_name']);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file['tmp_name']);
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'Gagal membaca file: ' . $e->getMessage();
            $this->redirect('siswa');
            return;
        }

        $model    = new Siswa();
        $imported = 0;
        $skipped  = 0;
        $sheetCount = 0;

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $rows = $sheet->toArray(null, true, true, false);
            if (count($rows) < 2) continue; // Skip sheet kosong / hanya header

            // Baris pertama = header, buat mapping kolom (case-insensitive)
            $header = array_map(fn($h) => strtolower(trim((string) $h)), $rows[0]);
            $colMap = array_flip($header);

            $required = ['nama', 'nisn', 'nis', 'jenis kelamin', 'tahun ajaran', 'kelas', 'semester'];
            $missing  = array_filter($required, fn($r) => !array_key_exists($r, $colMap));

            // Jika sheet ini tidak memiliki kolom yang sesuai, abaikan sheet ini
            if (!empty($missing)) continue;

            $sheetCount++;

            for ($i = 1; $i < count($rows); $i++) {
                $row  = $rows[$i];
                $nama = trim((string) ($row[$colMap['nama']] ?? ''));
                $nisn = trim((string) ($row[$colMap['nisn']] ?? ''));
                $nis  = trim((string) ($row[$colMap['nis']]  ?? ''));
                $jk   = strtoupper(trim((string) ($row[$colMap['jenis kelamin']] ?? '')));
                $ta   = trim((string) ($row[$colMap['tahun ajaran']] ?? ''));
                $kelas = trim((string) ($row[$colMap['kelas']] ?? ''));
                $semJenis = strtolower(trim((string) ($row[$colMap['semester']] ?? '')));

                if ($nama === '' || $nisn === '' || $nis === '') continue;
                if (!in_array($jk, ['L', 'P']))               continue;
                if ($ta === '' || $kelas === '')               continue;

                $semester = $model->hitungSemester($kelas, $semJenis);

                $result = $model->importRow([
                    'nama'          => $nama,
                    'nisn'          => $nisn,
                    'nis'           => $nis,
                    'jenis_kelamin' => $jk,
                    'tahun_ajaran'  => $ta,
                    'kelas'         => $kelas,
                    'semester'      => $semester,
                ]);

                $result ? $imported++ : $skipped++;
            }
        }

        if ($sheetCount === 0) {
            $_SESSION['error'] = 'Tidak ada sheet yang memiliki format kolom yang valid.';
            $this->redirect('siswa');
            return;
        }

        push_notif("$imported siswa berhasil diimport" .
                   ($skipped > 0 ? ", $skipped diabaikan (data sudah ada)." : '.'));

        $this->redirect('siswa');
    }
    
    public function template(): void
    {
        require_login();

        $autoload = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($autoload)) {
            $_SESSION['error'] = 'Library PhpSpreadsheet belum terinstall. Jalankan: composer install';
            $this->redirect('siswa');
            return;
        }

        require_once $autoload;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Identitas Siswa');

        // Headers
        $headers = ['Nama', 'NISN', 'NIS', 'Jenis Kelamin', 'Tahun Ajaran', 'Kelas', 'Semester'];
        $sheet->fromArray($headers, null, 'A1');

        // Styling
        $headerRange = 'A1:G1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        // Borders for header and dummy data
        $fullRange = 'A1:G2';
        $sheet->getStyle($fullRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Template_Data_Siswa.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        exit;
    }
}