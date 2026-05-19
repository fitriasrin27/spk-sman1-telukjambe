<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\RiwayatKelas;

class RiwayatKelasController extends Controller
{
    public function __construct()
    {
        \App\Core\RoleAccess::check('riwayat-kelas');
    }

    public function index(): void
    {
        require_login();

        $model = new RiwayatKelas();

        $tahunAjaran = $_GET['tahun_ajaran'] ?? '';
        $kelas       = $_GET['kelas']        ?? '';
        $semester    = (int) ($_GET['semester'] ?? 0);
        $search      = $_GET['q'] ?? '';
        $limit       = max(1, (int) ($_GET['limit'] ?? 10));
        $page        = max(1, (int) ($_GET['page']  ?? 1));
        $offset      = ($page - 1) * $limit;

        $result     = $model->getAll($tahunAjaran, $kelas, $semester, $offset, $limit, $search);
        $totalPages = (int) ceil($result['total'] / max(1, $limit));

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->view('riwayat-kelas/index', [
            'title'             => 'Riwayat Kelas',
            'riwayat'           => $result['data'],
            'total'             => $result['total'],
            'page'              => $page,
            'limit'             => $limit,
            'totalPages'        => $totalPages,
            'offset'            => $offset,
            'tahunAjaran'       => $tahunAjaran,
            'kelas'             => $kelas,
            'semester'          => $semester,
            'search'            => $search,
            'daftarTahunAjaran' => $model->getTahunAjaran(),
            'daftarKelas'       => $model->getKelas($tahunAjaran),
            'error'             => $error,
        ], 'layouts/app');
    }

    /** Tambah penempatan baru untuk siswa yang sudah ada. */
    public function store(): void
    {
        require_login();

        $model    = new RiwayatKelas();
        $errors   = [];
        $idSiswa  = (int) ($_POST['id_siswa'] ?? 0);
        $tahunAjaran = trim($_POST['tahun_ajaran'] ?? '');
        $kelas       = trim($_POST['kelas']        ?? '');
        $semJenis    = $_POST['semester_jenis']     ?? '';

        if ($idSiswa <= 0)          $errors[] = 'Siswa belum dipilih.';
        if ($tahunAjaran === '')     $errors[] = 'Tahun ajaran tidak boleh kosong.';
        if ($kelas === '')           $errors[] = 'Kelas tidak boleh kosong.';
        if ($semJenis === '')        $errors[] = 'Semester harus dipilih.';

        if (empty($errors)) {
            $semester = RiwayatKelas::hitungSemester($kelas, $semJenis);
            if ($model->isDuplicate($idSiswa, $tahunAjaran, $semester)) {
                $errors[] = 'Data penempatan untuk siswa ini di tahun ajaran dan semester yang sama sudah ada.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('riwayat-kelas');
            return;
        }

        $semester = RiwayatKelas::hitungSemester($kelas, $semJenis);
        $model->insert($idSiswa, $tahunAjaran, $kelas, $semester);

        push_notif('Penempatan kelas berhasil ditambahkan.');
        $this->redirect('riwayat-kelas');
    }

    /** Edit tahun ajaran, kelas, semester dari satu record riwayat. */
    public function update(): void
    {
        require_login();

        $model    = new RiwayatKelas();
        $errors   = [];
        $id          = (int) ($_POST['id_riwayat']   ?? 0);
        $tahunAjaran = trim($_POST['tahun_ajaran']   ?? '');
        $kelas       = trim($_POST['kelas']          ?? '');
        $semJenis    = $_POST['semester_jenis']       ?? '';

        if ($id <= 0)           $errors[] = 'Data tidak valid.';
        if ($tahunAjaran === '') $errors[] = 'Tahun ajaran tidak boleh kosong.';
        if ($kelas === '')       $errors[] = 'Kelas tidak boleh kosong.';
        if ($semJenis === '')    $errors[] = 'Semester harus dipilih.';

        if (empty($errors)) {
            // Ambil id_siswa dari record yang sedang diedit
            $existing = $model->findById($id);
            if (!$existing) {
                $errors[] = 'Data riwayat tidak ditemukan.';
            } else {
                $semester = RiwayatKelas::hitungSemester($kelas, $semJenis);
                if ($model->isDuplicate((int) $existing['id_siswa'], $tahunAjaran, $semester, $id)) {
                    $errors[] = 'Data penempatan untuk siswa ini di tahun ajaran dan semester yang sama sudah ada.';
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('riwayat-kelas');
            return;
        }

        $semester = RiwayatKelas::hitungSemester($kelas, $semJenis);
        $model->update($id, $tahunAjaran, $kelas, $semester);

        push_notif('Data penempatan kelas berhasil diperbarui.');
        $this->redirect('riwayat-kelas');
    }

    /** Hapus satu record riwayat_kelas (tidak menyentuh tabel siswa). */
    public function delete(): void
    {
        require_login();

        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            (new RiwayatKelas())->delete($id);
            push_notif('Data penempatan kelas berhasil dihapus.');
        }

        $this->redirect('riwayat-kelas');
    }

    public function deleteBatch(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('riwayat-kelas');
            return;
        }

        $ids = $_POST['ids'] ?? [];
        if (empty($ids)) {
            $_SESSION['error'] = 'Tidak ada data yang dipilih.';
            $this->redirect('riwayat-kelas');
            return;
        }

        $model = new RiwayatKelas();
        $db = \App\Core\Database::connect();
        try {
            $db->beginTransaction();
            foreach ($ids as $id) {
                $model->delete((int)$id);
            }
            $db->commit();
            push_notif(count($ids) . ' data penempatan kelas berhasil dihapus.');
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
        }
        $this->redirect('riwayat-kelas');
    }

    /** Import penempatan kelas dari file Excel. */
    public function import(): void
    {
        require_login();

        if (empty($_FILES['excel_file']['tmp_name'])) {
            $_SESSION['error'] = 'Pilih file Excel terlebih dahulu.';
            $this->redirect('riwayat-kelas');
            return;
        }

        $file = $_FILES['excel_file'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['xlsx', 'xls'])) {
            $_SESSION['error'] = 'Format tidak valid. Gunakan .xlsx atau .xls';
            $this->redirect('riwayat-kelas');
            return;
        }

        $autoload = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($autoload)) {
            $_SESSION['error'] = 'Library PhpSpreadsheet belum terinstall. Jalankan: composer install';
            $this->redirect('riwayat-kelas');
            return;
        }

        require_once $autoload;

        try {
            $reader      = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file['tmp_name']);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file['tmp_name']);
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'Gagal membaca file: ' . $e->getMessage();
            $this->redirect('riwayat-kelas');
            return;
        }

        $model    = new RiwayatKelas();
        $inserted = 0;
        $skipped  = 0; // duplikat
        $notFound = 0; // NISN tidak ada di identitas siswa
        $sheetCount = 0;

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $rows = $sheet->toArray(null, true, true, false);
            if (count($rows) < 2) continue; // Skip sheet kosong / hanya header

            $header   = array_map(fn($h) => strtolower(trim((string) $h)), $rows[0]);
            $colMap   = array_flip($header);
            $required = ['nisn', 'tahun ajaran', 'kelas', 'semester'];
            $missing  = array_filter($required, fn($r) => !array_key_exists($r, $colMap));

            // Jika sheet ini tidak memiliki kolom yang sesuai, abaikan sheet ini
            if (!empty($missing)) continue;

            $sheetCount++;

            for ($i = 1; $i < count($rows); $i++) {
                $row      = $rows[$i];
                $nisn     = trim((string) ($row[$colMap['nisn']]          ?? ''));
                $ta       = trim((string) ($row[$colMap['tahun ajaran']]  ?? ''));
                $kelas    = trim((string) ($row[$colMap['kelas']]         ?? ''));
                $semJenis = strtolower(trim((string) ($row[$colMap['semester']] ?? '')));

                if ($nisn === '' || $ta === '' || $kelas === '') continue;

                $semester = RiwayatKelas::hitungSemester($kelas, $semJenis);
                $result   = $model->importRow($nisn, $ta, $kelas, $semester);

                match ($result) {
                    'inserted'  => $inserted++,
                    'duplicate' => $skipped++,
                    'not_found' => $notFound++,
                };
            }
        }

        if ($sheetCount === 0) {
            $_SESSION['error'] = 'Tidak ada sheet yang memiliki format kolom yang valid.';
            $this->redirect('riwayat-kelas');
            return;
        }

        $msg = "$inserted penempatan berhasil diimport";
        if ($skipped > 0)   $msg .= ", $skipped duplikat dilewati";
        if ($notFound > 0)  $msg .= ", $notFound NISN tidak ditemukan";
        $msg .= '.';

        push_notif($msg);
        $this->redirect('riwayat-kelas');
    }

    /** AJAX: cari siswa berdasarkan nama (autocomplete). */
    public function searchSiswa(): void
    {
        require_login();

        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        $results = (new RiwayatKelas())->searchSiswaByNama($q);
        header('Content-Type: application/json');
        echo json_encode($results);
    }

    public function template(): void
    {
        require_login();

        $autoload = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($autoload)) {
            $_SESSION['error'] = 'Library PhpSpreadsheet belum terinstall. Jalankan: composer install';
            $this->redirect('riwayat-kelas');
            return;
        }

        require_once $autoload;

        $tahunAjaran = trim($_GET['tahun_ajaran'] ?? '');
        $kelas = trim($_GET['kelas'] ?? '');
        $semesterInput = trim($_GET['semester'] ?? '');

        if ($tahunAjaran === '' || $kelas === '' || $semesterInput === '') {
            $_SESSION['error'] = 'Pilih Tahun Ajaran, Kelas, dan Semester untuk mendownload template.';
            $this->redirect('riwayat-kelas');
            return;
        }

        if (strtolower($semesterInput) === 'ganjil' || strtolower($semesterInput) === 'genap') {
            $semesterNumber = \App\Models\RiwayatKelas::hitungSemester($kelas, $semesterInput);
        } else {
            $semesterNumber = (int)$semesterInput;
        }

        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("
            SELECT s.nama, s.nisn
            FROM riwayat_kelas rk
            JOIN siswa s ON rk.id_siswa = s.id_siswa
            WHERE rk.tahun_ajaran = :ta AND rk.kelas = :kelas AND rk.semester = :sem
            ORDER BY s.nama ASC
        ");
        $stmt->execute([':ta' => $tahunAjaran, ':kelas' => $kelas, ':sem' => $semesterNumber]);
        $siswaList = $stmt->fetchAll();

        if (empty($siswaList)) {
            $_SESSION['error'] = 'Tidak ada siswa yang terdaftar di kelas tersebut pada semester yang dipilih.';
            $this->redirect('riwayat-kelas');
            return;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Kenaikan Kelas');

        $headers = ['Nama', 'NISN', 'Tahun Ajaran', 'Kelas', 'Semester'];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($siswaList as $s) {
            $sheet->fromArray(
                [$s['nama'], $s['nisn'], '', '', ''],
                null,
                'A' . $row
            );
            $row++;
        }

        $highestRow = $row - 1;
        $fullRange = 'A1:E' . $highestRow;

        // Styling Header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:E1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        // Borders
        $sheet->getStyle($fullRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Template_Kenaikan_Kelas_' . str_replace(['/', '\\'], '-', $tahunAjaran) . '_' . str_replace(' ', '_', $kelas) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        exit;
    }
}