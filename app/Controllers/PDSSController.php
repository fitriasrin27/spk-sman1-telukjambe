<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use PDO;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Dompdf\Dompdf;

class PDSSController extends Controller
{
    public function __construct()
    {
        \App\Core\RoleAccess::check('perhitungan');
    }

    /**
     * Endpoint AJAX untuk mendapatkan daftar mapel unik (untuk modal checklist)
     */
    public function getMapel(): void
    {
        require_login();
        header('Content-Type: application/json');

        $db = Database::connect();
        
        // Perbaikan Query: Hindari ONLY_FULL_GROUP_BY dengan filter unik di level PHP
        $sql = "
            SELECT id_mapel, kode_mapel, nama_mapel 
            FROM mata_pelajaran 
            WHERE kode_mapel != '' AND kode_mapel IS NOT NULL
            ORDER BY " . \App\Models\MataPelajaran::orderPakemSql('nama_mapel', 'kode_mapel', 'id_mapel') . "
        ";
        
        try {
            $stmt = $db->query($sql);
            $allMapel = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $mapel = [];
            $seen = [];
            foreach ($allMapel as $m) {
                if (!isset($seen[$m['kode_mapel']])) {
                    $seen[$m['kode_mapel']] = true;
                    $mapel[] = $m;
                }
            }
            
            echo json_encode($mapel);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error DB: ' . $e->getMessage()]);
        }
    }

    /**
     * Export File 1: Daftar NISN Eligible
     */
    public function exportEligible(): void
    {
        require_login();
        $idPerhitungan = (int)($_GET['id'] ?? 0);
        $format        = $_GET['format'] ?? 'csv';

        if ($idPerhitungan <= 0) {
            die('ID Perhitungan tidak valid.');
        }

        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM perhitungan WHERE id_perhitungan = :id");
        $stmt->execute([':id' => $idPerhitungan]);
        $riwayat = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$riwayat) die('Data perhitungan tidak ditemukan.');

        $stmtH = $db->prepare("
            SELECT s.nisn, s.nama
            FROM hasil_perhitungan h
            JOIN siswa s ON h.id_siswa = s.id_siswa
            WHERE h.id_perhitungan = :id AND h.status_eligible = 'ya'
            ORDER BY h.ranking ASC
        ");
        $stmtH->execute([':id' => $idPerhitungan]);
        $hasil = $stmtH->fetchAll(PDO::FETCH_ASSOC);

        $filename = "PDSS_Eligible_{$riwayat['jurusan']}_" . str_replace('/', '-', $riwayat['tahun_ajaran']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Baris 1: nisn
        $sheet->setCellValueExplicit('A1', 'nisn', DataType::TYPE_STRING);

        $rowNum = 2;
        foreach ($hasil as $row) {
            // Memaksa cell menjadi Tipe String agar 00 di depan tidak hilang
            $sheet->setCellValueExplicit('A' . $rowNum, $row['nisn'], DataType::TYPE_STRING);
            $rowNum++;
        }

        log_activity("Mengekspor data NISN siswa eligible PDSS ({$riwayat['jurusan']}, Angkatan: {$riwayat['tahun_ajaran']}) ke format " . strtoupper($format), 'laporan');

        push_notif("Data NISN siswa eligible PDSS ({$riwayat['jurusan']} - {$riwayat['tahun_ajaran']}) berhasil diekspor.");

        $this->downloadSpreadsheet($spreadsheet, $filename, $format);
    }

    /**
     * Export File 2: Daftar Nilai Horizontal
     */
    public function exportNilai(): void
    {
        require_login();
        $idPerhitungan = (int)($_POST['id_perhitungan'] ?? 0);
        $tingkat       = (int)($_POST['tingkat'] ?? 10);
        $smt           = (int)($_POST['semester'] ?? 1);
        $jurusan       = $_POST['jurusan'] ?? 'MIPA';
        $mapelCodes    = $_POST['mapel_codes'] ?? ''; 
        $format        = $_POST['format'] ?? 'csv';

        if ($idPerhitungan <= 0 || empty($mapelCodes)) {
            die('Parameter tidak lengkap.');
        }

        // Terjemahkan tingkat & semester ke struktur DB (1-6)
        $dbSemester = 1;
        if ($tingkat === 10) $dbSemester = ($smt === 1) ? 1 : 2;
        elseif ($tingkat === 11) $dbSemester = ($smt === 1) ? 3 : 4;
        elseif ($tingkat === 12) $dbSemester = ($smt === 1) ? 5 : 6;

        $mapelArray = array_filter(array_map('trim', explode(',', $mapelCodes)));

        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM perhitungan WHERE id_perhitungan = :id");
        $stmt->execute([':id' => $idPerhitungan]);
        $riwayat = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$riwayat) die('Data perhitungan tidak ditemukan.');

        $sql = "
            SELECT s.nisn, mp.kode_mapel, n.nilai
            FROM hasil_perhitungan hp
            JOIN siswa s ON hp.id_siswa = s.id_siswa
            JOIN riwayat_kelas rk_target ON s.id_siswa = rk_target.id_siswa AND rk_target.semester = :semester
            JOIN nilai n ON rk_target.id_riwayat = n.id_riwayat
            JOIN mata_pelajaran mp ON n.id_mapel = mp.id_mapel
            WHERE hp.id_perhitungan = :id AND hp.status_eligible = 'ya'
        ";
        $stmtNilai = $db->prepare($sql);
        $stmtNilai->execute([':id' => $idPerhitungan, ':semester' => $dbSemester]);
        $rawData = $stmtNilai->fetchAll(PDO::FETCH_ASSOC);

        $pivotData = [];
        $nisnList = [];
        
        $stmtNisn = $db->prepare("
            SELECT s.nisn 
            FROM hasil_perhitungan hp
            JOIN siswa s ON hp.id_siswa = s.id_siswa
            WHERE hp.id_perhitungan = :id AND hp.status_eligible = 'ya'
            ORDER BY hp.ranking ASC
        ");
        $stmtNisn->execute([':id' => $idPerhitungan]);
        while ($row = $stmtNisn->fetch(PDO::FETCH_ASSOC)) {
            $nisn = $row['nisn'];
            $nisnList[] = $nisn;
            $pivotData[$nisn] = [];
            foreach ($mapelArray as $kode) {
                $pivotData[$nisn][$kode] = ''; 
            }
        }

        foreach ($rawData as $row) {
            $nisn = $row['nisn'];
            $kodeMapel = $row['kode_mapel'];
            if (in_array($kodeMapel, $mapelArray) && isset($pivotData[$nisn])) {
                $pivotData[$nisn][$kodeMapel] = $row['nilai'];
            }
        }

        $filename = "PDSS_Nilai_{$jurusan}_Kls{$tingkat}_Smt{$smt}_" . str_replace('/', '-', $riwayat['tahun_ajaran']);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Baris 1: Judul
        $title = "Data Nilai {$jurusan} - Kelas {$tingkat} - Semester {$smt}";
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:E1');

        // Baris 2: Header Kolom
        $sheet->setCellValue('A2', 'nisn');
        $colIndex = 2; // Col B
        foreach ($mapelArray as $kode) {
            $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . '2';
            $sheet->setCellValue($cellCoord, $kode);
            $colIndex++;
        }

        // Baris 3 dst: Data
        $rowNum = 3;
        foreach ($nisnList as $nisn) {
            // Memaksa NISN menjadi String agar '00' tidak hilang
            $sheet->setCellValueExplicit('A' . $rowNum, $nisn, DataType::TYPE_STRING);
            
            $colIndex = 2;
            foreach ($mapelArray as $kode) {
                $cellCoord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $rowNum;
                $sheet->setCellValue($cellCoord, $pivotData[$nisn][$kode]);
                $colIndex++;
            }
            $rowNum++;
        }

        log_activity("Mengekspor data nilai PDSS ({$jurusan}, Kelas: {$tingkat}, Semester: {$smt}, Angkatan: {$riwayat['tahun_ajaran']}) ke format " . strtoupper($format), 'laporan');

        push_notif("Data nilai PDSS ({$jurusan} - Kelas {$tingkat} Semester {$smt}) berhasil diekspor.");

        $this->downloadSpreadsheet($spreadsheet, $filename, $format);
    }

    /**
     * Helper untuk handle format file output
     */
    private function downloadSpreadsheet($spreadsheet, string $filename, string $format)
    {
        if ($format === 'xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        } elseif ($format === 'pdf') {
            $sheet = $spreadsheet->getActiveSheet();
            $totalCols = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());
            
            $html = '
            <html>
            <head>
                <style>
                    body { font-family: "Times New Roman", Times, serif; color: #333; margin: 0; padding: 0; }
                    .header-container { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #198754; padding-bottom: 10px; }
                    h2 { margin: 0; font-size: 20px; color: #198754; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
                    p.sub { margin: 5px 0 0 0; font-size: 12px; color: #6c757d; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; }
                    th, td { border: 1px solid #dee2e6; padding: 8px 6px; }
                    th { background-color: #f1f8f5; font-weight: bold; text-align: center; color: #198754; }
                    tr.title-row th { background-color: #198754; color: #ffffff; text-align: center; font-size: 13px; letter-spacing: 0.5px; padding: 10px; }
                    tr:nth-child(even) td { background-color: #fafbfc; }
                    .td-center { text-align: center; }
                    .td-left { text-align: left; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class="header-container">
                    <h2>Laporan Ekspor Data Nilai PDSS</h2>
                    <p class="sub">Sistem Pendukung Keputusan SNBP - SMAN 1 Telukjambe</p>
                </div>
                <table>
            ';
            
            $rowIndex = 1;
            foreach ($sheet->getRowIterator() as $row) {
                if ($rowIndex === 1) {
                    $html .= '<tr class="title-row">';
                } else {
                    $html .= '<tr>';
                }
                
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $val = $cell->getFormattedValue();
                    if ($rowIndex === 1) {
                        if ($cell->getColumn() !== 'A') continue; 
                        $html .= "<th colspan=\"{$totalCols}\">" . htmlspecialchars((string)$val) . '</th>';
                    } elseif ($rowIndex === 2) {
                        $html .= "<th>" . htmlspecialchars(strtoupper((string)$val)) . '</th>';
                    } else {
                        $html .= "<td class=\"td-center\">" . htmlspecialchars((string)$val) . '</td>';
                    }
                }
                $html .= '</tr>';
                $rowIndex++;
            }
            $html .= '</table>';
            
            $tz = new \DateTimeZone('Asia/Jakarta');
            $dt = new \DateTime('now', $tz);
            $timeStr = $dt->format('d F Y, H:i');
            
            $html .= '<p style="font-size: 10px; color: #adb5bd; margin-top: 15px; text-align: right; font-style: italic;">Dicetak secara otomatis oleh sistem pada: ' . $timeStr . ' WIB</p>';
            $html .= '</body></html>';

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $filename . '.pdf"');
            echo $dompdf->output();
        } else {
            // Default: CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            // Tambahkan BOM agar Excel membaca UTF-8 dengan benar
            $writer->setUseBOM(true); 
            $writer->save('php://output');
        }
        exit;
    }
}
