<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\RiwayatKelas;

class NilaiController extends Controller
{
    public function __construct()
    {
        \App\Core\RoleAccess::check('nilai');
    }

    public function index(): void
    {
        require_login();

        $nilaiModel = new Nilai();

        $tahunAjaran = trim($_GET['tahun_ajaran'] ?? '');
        $kelas       = trim($_GET['kelas'] ?? '');
        $semester    = trim($_GET['semester'] ?? '');
        $status      = trim($_GET['status'] ?? '');
        $tingkat     = trim($_GET['tingkat'] ?? '');
        $search      = $_GET['q'] ?? '';
        
        $limit = (int)($_GET['limit'] ?? 10);
        if (!in_array($limit, [10, 25, 50, 100])) {
            $limit = 10;
        }

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $rekapData = $nilaiModel->getRekapNilai($tahunAjaran, $kelas, $semester, $limit, $offset, $status, $tingkat, $search);
        $nilaiList = $rekapData['data'];
        $total     = $rekapData['total'];
        $totalPages = ceil($total / $limit);

        // Opsi filter
        $optTahunAjaran = $nilaiModel->getTahunAjaran();
        $optKelas       = $nilaiModel->getKelas($tahunAjaran);
        $optSemester    = $nilaiModel->getSemester($tahunAjaran, $kelas);

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->view('nilai/index', [
            'title'          => 'Data Nilai',
            'nilaiList'      => $nilaiList,
            'total'          => $total,
            'limit'          => $limit,
            'page'           => $page,
            'totalPages'     => $totalPages,
            'offset'         => $offset,
            'tahunAjaran'    => $tahunAjaran,
            'kelas'          => $kelas,
            'semester'       => $semester,
            'status'         => $status,
            'tingkat'        => $tingkat,
            'search'         => $search,
            'optTahunAjaran' => $optTahunAjaran,
            'optKelas'       => $optKelas,
            'optSemester'    => $optSemester,
            'error'          => $error
        ], 'layouts/app');
    }

    public function getRiwayatSiswa(): void
    {
        require_login();
        $idSiswa = (int)($_GET['id_siswa'] ?? 0);
        
        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("SELECT id_riwayat, tahun_ajaran, kelas, semester FROM riwayat_kelas WHERE id_siswa = :id ORDER BY tahun_ajaran DESC, semester DESC");
        $stmt->execute([':id' => $idSiswa]);
        
        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll());
        exit;
    }

    public function getMapelByRiwayat(): void
    {
        require_login();
        $idRiwayat = (int)($_GET['id_riwayat'] ?? 0);

        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("SELECT kelas FROM riwayat_kelas WHERE id_riwayat = :id");
        $stmt->execute([':id' => $idRiwayat]);
        $rk = $stmt->fetch();

        if (!$rk) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }

        $kelas = strtoupper(trim($rk['kelas']));
        [$tingkat, $jurusan] = $this->inferTingkatJurusan($kelas);

        $orderMapel = MataPelajaran::orderPakemSql('nama_mapel');
        $stmtMapel = $db->prepare("SELECT id_mapel, kode_mapel, nama_mapel FROM mata_pelajaran WHERE tingkat = :tingkat AND jurusan = :jurusan ORDER BY $orderMapel");
        $stmtMapel->execute([':tingkat' => $tingkat, ':jurusan' => $jurusan]);
        $mapelList = $stmtMapel->fetchAll();

        $stmtNilai = $db->prepare("SELECT id_mapel, nilai FROM nilai WHERE id_riwayat = :id");
        $stmtNilai->execute([':id' => $idRiwayat]);
        $nilaiExist = [];
        foreach ($stmtNilai->fetchAll() as $n) {
            $nilaiExist[$n['id_mapel']] = $n['nilai'];
        }

        $stmtAbsen = $db->prepare("SELECT sakit, izin, alpa FROM absensi WHERE id_riwayat = :id");
        $stmtAbsen->execute([':id' => $idRiwayat]);
        $absenExist = $stmtAbsen->fetch();

        $stmtEkskul = $db->prepare("SELECT id_ekskul, nama_ekskul, predikat FROM ekstrakurikuler WHERE id_riwayat = :id");
        $stmtEkskul->execute([':id' => $idRiwayat]);
        $ekskulExist = $stmtEkskul->fetchAll();

        $stmtPrestasi = $db->prepare("SELECT id_prestasi, nama_prestasi, tingkat FROM prestasi WHERE id_riwayat = :id");
        $stmtPrestasi->execute([':id' => $idRiwayat]);
        $prestasiExist = $stmtPrestasi->fetchAll();

        header('Content-Type: application/json');
        echo json_encode([
            'mapel'    => $mapelList,
            'nilai'    => $nilaiExist,
            'absen'    => $absenExist ?: ['sakit' => '', 'izin' => '', 'alpa' => ''],
            'ekskul'   => $ekskulExist,
            'prestasi' => $prestasiExist
        ]);
        exit;
    }

    public function store(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('nilai');
            return;
        }

        $idRiwayat = (int)($_POST['id_riwayat'] ?? 0);
        if ($idRiwayat === 0) {
            $_SESSION['error'] = 'Riwayat kelas tidak valid.';
            $this->redirect('nilai');
            return;
        }

        $db = \App\Core\Database::connect();
        try {
            $db->beginTransaction();
            $this->saveNilaiData(
                $db,
                $idRiwayat,
                $_POST['nilai'] ?? [],
                $_POST['absen'] ?? [],
                $_POST['ekskul'] ?? [],
                $_POST['prestasi'] ?? []
            );
            $db->commit();
            push_notif('Data nilai ' . $this->getNamaSiswaByRiwayat($db, $idRiwayat) . ' berhasil diperbarui.');
            if (($_POST['redirect_to'] ?? '') === 'detail') {
                $this->redirect('nilai/detail&id=' . $idRiwayat);
                return;
            }
            $this->redirect('nilai');
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Gagal menyimpan data: ' . $e->getMessage();
            $this->redirect('nilai');
        }
    }

    private function saveNilaiData(
        \PDO $db,
        int $idRiwayat,
        array $nilaiInput,
        array $absenInput,
        array $ekskulInput,
        array $prestasiInput,
        bool $replaceEkskul = true,
        bool $replacePrestasi = true,
        bool $saveAbsensi = true
    ): void {
        $upsertNilai = $db->prepare("INSERT INTO nilai (id_riwayat, id_mapel, nilai) VALUES (:id_riwayat, :id_mapel, :nilai) ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)");
        $deleteNilai = $db->prepare('DELETE FROM nilai WHERE id_riwayat = :id_riwayat AND id_mapel = :id_mapel');

        foreach ($nilaiInput as $n) {
            $idMapel = (int)($n['id_mapel'] ?? 0);
            $nilaiAngka = $this->toNullableDecimal($n['nilai_angka'] ?? null);
            if ($idMapel <= 0) continue;
            if ($nilaiAngka === null) {
                $deleteNilai->execute([':id_riwayat' => $idRiwayat, ':id_mapel' => $idMapel]);
                continue;
            }
            if ($nilaiAngka < 0 || $nilaiAngka > 100) throw new \InvalidArgumentException('Nilai harus 0-100.');
            $upsertNilai->execute([':id_riwayat' => $idRiwayat, ':id_mapel' => $idMapel, ':nilai' => $nilaiAngka]);
        }

        if ($saveAbsensi) {
            $stmtAbsen = $db->prepare("INSERT INTO absensi (id_riwayat, sakit, izin, alpa) VALUES (:id_riwayat, :sakit, :izin, :alpa) ON DUPLICATE KEY UPDATE sakit = VALUES(sakit), izin = VALUES(izin), alpa = VALUES(alpa)");
            $stmtAbsen->execute([
                ':id_riwayat' => $idRiwayat,
                ':sakit'      => $this->toNonNegativeInt($absenInput['sakit'] ?? 0),
                ':izin'       => $this->toNonNegativeInt($absenInput['izin'] ?? 0),
                ':alpa'       => $this->toNonNegativeInt($absenInput['alpa'] ?? 0),
            ]);
        }

        if ($replaceEkskul) {
            $db->prepare("DELETE FROM ekstrakurikuler WHERE id_riwayat = :id")->execute([':id' => $idRiwayat]);
            $insEks = $db->prepare("INSERT INTO ekstrakurikuler (id_riwayat, nama_ekskul, predikat) VALUES (:id, :nama, :predikat)");
            foreach ($ekskulInput as $eks) {
                if (trim($eks['nama'] ?? '') !== '' && trim($eks['predikat'] ?? '') !== '') {
                    $insEks->execute([':id' => $idRiwayat, ':nama' => trim($eks['nama']), ':predikat' => strtoupper(trim($eks['predikat']))]);
                }
            }
        }

        if ($replacePrestasi) {
            $db->prepare("DELETE FROM prestasi WHERE id_riwayat = :id")->execute([':id' => $idRiwayat]);
            $insPres = $db->prepare("INSERT INTO prestasi (id_riwayat, nama_prestasi, tingkat) VALUES (:id, :nama, :tingkat)");
            foreach ($prestasiInput as $pres) {
                if (trim($pres['nama'] ?? '') !== '' && trim($pres['tingkat'] ?? '') !== '') {
                    $insPres->execute([':id' => $idRiwayat, ':nama' => trim($pres['nama']), ':tingkat' => trim($pres['tingkat'])]);
                }
            }
        }
    }

    private function toNullableDecimal(mixed $value): ?float
    {
        if ($value === null) return null;
        $value = str_replace(',', '.', trim((string)$value));
        return is_numeric($value) ? (float)$value : null;
    }

    private function toNonNegativeInt(mixed $value): int
    {
        return max(0, (int)trim((string)($value ?? 0)));
    }

    private function getNamaSiswaByRiwayat(\PDO $db, int $idRiwayat): string
    {
        $stmt = $db->prepare("SELECT s.nama FROM riwayat_kelas r JOIN siswa s ON r.id_siswa = s.id_siswa WHERE r.id_riwayat = :id");
        $stmt->execute([':id' => $idRiwayat]);
        return (string)($stmt->fetchColumn() ?: 'Siswa');
    }

    private function getDetailData(int $idRiwayat): ?array
    {
        $db = \App\Core\Database::connect();
        $stmt = $db->prepare("SELECT r.*, s.nama, s.nisn, s.nis, s.jenis_kelamin FROM riwayat_kelas r JOIN siswa s ON r.id_siswa = s.id_siswa WHERE r.id_riwayat = :id");
        $stmt->execute([':id' => $idRiwayat]);
        $riwayat = $stmt->fetch();
        if (!$riwayat) return null;
        
        $kelas = strtoupper(trim($riwayat['kelas']));
        [$tingkat, $jurusan] = $this->inferTingkatJurusan($kelas);
        
        $orderMapel = MataPelajaran::orderPakemSql('nama_mapel');
        $stmtMapel = $db->prepare("SELECT id_mapel, kode_mapel, nama_mapel FROM mata_pelajaran WHERE tingkat = :tingkat AND jurusan = :jurusan ORDER BY $orderMapel");
        $stmtMapel->execute([':tingkat' => $tingkat, ':jurusan' => $jurusan]);
        $mapelList = $stmtMapel->fetchAll();
        
        $stmtNilai = $db->prepare("SELECT id_mapel, nilai FROM nilai WHERE id_riwayat = :id");
        $stmtNilai->execute([':id' => $idRiwayat]);
        $nilaiList = [];
        foreach ($stmtNilai->fetchAll() as $n) {
            $nilaiList[$n['id_mapel']] = $n['nilai'];
        }
        
        $stmtAbsen = $db->prepare("SELECT sakit, izin, alpa FROM absensi WHERE id_riwayat = :id");
        $stmtAbsen->execute([':id' => $idRiwayat]);
        $absen = $stmtAbsen->fetch() ?: ['sakit' => 0, 'izin' => 0, 'alpa' => 0];
        
        $stmtEkskul = $db->prepare("SELECT id_ekskul, nama_ekskul, predikat FROM ekstrakurikuler WHERE id_riwayat = :id");
        $stmtEkskul->execute([':id' => $idRiwayat]);
        $ekskul = $stmtEkskul->fetchAll();
        
        $stmtPrestasi = $db->prepare("SELECT id_prestasi, nama_prestasi, tingkat FROM prestasi WHERE id_riwayat = :id");
        $stmtPrestasi->execute([':id' => $idRiwayat]);
        $prestasi = $stmtPrestasi->fetchAll();
        
        $totalNilai = array_sum($nilaiList);
        $rataRata = count($mapelList) > 0 ? $totalNilai / count($mapelList) : 0;
        
        return [
            'riwayat'  => $riwayat, 'mapel' => $mapelList, 'nilai' => $nilaiList,
            'absen'    => $absen, 'ekskul' => $ekskul, 'prestasi' => $prestasi,
            'total'    => $totalNilai, 'rataRata' => $rataRata
        ];
    }

    public function detail(): void
    {
        require_login();
        $data = $this->getDetailData((int)($_GET['id'] ?? 0));
        if (!$data) {
            $_SESSION['error'] = 'Data tidak ditemukan.';
            $this->redirect('nilai');
            return;
        }
        $this->view('nilai/detail', array_merge(['title' => 'Detail Nilai Siswa'], $data), 'layouts/app');
    }

    public function edit(): void
    {
        require_login();
        $data = $this->getDetailData((int)($_GET['id'] ?? 0));
        if (!$data) {
            $_SESSION['error'] = 'Data tidak ditemukan.';
            $this->redirect('nilai');
            return;
        }
        $this->view('nilai/edit', array_merge(['title' => 'Edit Nilai Siswa'], $data), 'layouts/app');
    }

    public function update(): void
    {
        $this->store();
    }

    public function deleteBatch(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('nilai');
            return;
        }

        $ids = $_POST['ids'] ?? [];
        if (empty($ids)) {
            $_SESSION['error'] = 'Tidak ada data yang dipilih.';
            $this->redirect('nilai');
            return;
        }

        $db = \App\Core\Database::connect();
        try {
            $db->beginTransaction();
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $db->prepare("DELETE FROM nilai WHERE id_riwayat IN ($placeholders)")->execute($ids);
            $db->prepare("DELETE FROM absensi WHERE id_riwayat IN ($placeholders)")->execute($ids);
            $db->prepare("DELETE FROM ekstrakurikuler WHERE id_riwayat IN ($placeholders)")->execute($ids);
            $db->prepare("DELETE FROM prestasi WHERE id_riwayat IN ($placeholders)")->execute($ids);
            $db->commit();
            push_notif(count($ids) . ' data nilai berhasil dihapus.');
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
        }
        $this->redirect('nilai');
    }

    public function delete(): void
    {
        require_login();
        $idRiwayat = (int)($_GET['id'] ?? 0);
        if ($idRiwayat === 0) {
            $_SESSION['error'] = 'ID Riwayat tidak valid.';
            $this->redirect('nilai');
            return;
        }
        $db = \App\Core\Database::connect();
        try {
            $db->beginTransaction();
            $sStmt = $db->prepare("SELECT s.nama FROM riwayat_kelas r JOIN siswa s ON r.id_siswa = s.id_siswa WHERE r.id_riwayat = :id");
            $sStmt->execute([':id' => $idRiwayat]);
            $namaSiswa = $sStmt->fetchColumn() ?: 'Siswa';
            $db->prepare("DELETE FROM nilai WHERE id_riwayat = :id")->execute([':id' => $idRiwayat]);
            $db->prepare("DELETE FROM absensi WHERE id_riwayat = :id")->execute([':id' => $idRiwayat]);
            $db->prepare("DELETE FROM ekstrakurikuler WHERE id_riwayat = :id")->execute([':id' => $idRiwayat]);
            $db->prepare("DELETE FROM prestasi WHERE id_riwayat = :id")->execute([':id' => $idRiwayat]);
            $db->commit();
            push_notif("Data nilai {$namaSiswa} berhasil dihapus.");
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
        }
        $this->redirect('nilai');
    }

    private function normalizeImportHeader(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(['_', '-', '.', '/', '\\'], ' ', $value);
        return preg_replace('/\s+/', '', $value) ?: '';
    }

    private function inferTingkatJurusan(string $kelas): array
    {
        $kelasUpper = strtoupper(trim($kelas));
        $tingkat = 'X';
        if (str_starts_with($kelasUpper, 'XII')) $tingkat = 'XII';
        elseif (str_starts_with($kelasUpper, 'XI')) $tingkat = 'XI';
        $jurusan = str_contains($kelasUpper, 'MIPA') ? 'MIPA' : 'IPS';
        return [$tingkat, $jurusan];
    }

    private function normalizeImportSemester(string $kelas, mixed $semester): int
    {
        $raw = strtolower(trim((string)$semester));
        if ($raw === '') return 0;
        if (is_numeric($raw)) {
            $number = (int)$raw;
            if ($number >= 3 && $number <= 6) return $number;
            if ($number === 1 || $number === 2) return RiwayatKelas::hitungSemester($kelas, $number === 2 ? 'genap' : 'ganjil');
        }
        if (str_contains($raw, 'genap') || preg_match('/\b2\b/', $raw)) return RiwayatKelas::hitungSemester($kelas, 'genap');
        if (str_contains($raw, 'ganjil') || preg_match('/\b1\b/', $raw)) return RiwayatKelas::hitungSemester($kelas, 'ganjil');
        return 0;
    }

    private function getMapelImportIndex(\PDO $db): array
    {
        $stmt = $db->query("SELECT id_mapel, kode_mapel, nama_mapel, tingkat, jurusan FROM mata_pelajaran");
        $index = [];
        foreach ($stmt->fetchAll() as $mapel) {
            $group = strtoupper($mapel['tingkat']) . '|' . strtoupper($mapel['jurusan']);
            foreach ([$mapel['kode_mapel'], $mapel['nama_mapel']] as $label) {
                $key = $this->normalizeImportHeader((string)$label);
                if ($key !== '') $index[$group][$key] = (int)$mapel['id_mapel'];
            }
        }
        return $index;
    }

    private function findRiwayatForImport(\PDO $db, string $nisn, string $nama, string $tahunAjaran, string $kelas, int $semester): int
    {
        $stmt = $db->prepare("SELECT rk.id_riwayat FROM riwayat_kelas rk JOIN siswa s ON s.id_siswa = rk.id_siswa WHERE s.nisn = :nisn AND rk.tahun_ajaran = :ta AND rk.kelas = :kelas AND rk.semester = :sem LIMIT 1");
        $stmt->execute([':nisn' => $nisn, ':ta' => $tahunAjaran, ':kelas' => $kelas, ':sem' => $semester]);
        $id = (int)$stmt->fetchColumn();
        if ($id > 0) return $id;
        if ($nama !== '') {
            $stmt = $db->prepare("SELECT rk.id_riwayat FROM riwayat_kelas rk JOIN siswa s ON s.id_siswa = rk.id_siswa WHERE REPLACE(s.nama, ' ', '') = REPLACE(:nama, ' ', '') AND rk.tahun_ajaran = :ta AND REPLACE(rk.kelas, ' ', '') = REPLACE(:kelas, ' ', '') AND rk.semester = :sem LIMIT 1");
            $stmt->execute([':nama' => $nama, ':ta' => $tahunAjaran, ':kelas' => $kelas, ':sem' => $semester]);
            return (int)$stmt->fetchColumn();
        }
        return 0;
    }

    public function import(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file_excel']['tmp_name'])) {
            $_SESSION['error'] = 'Pilih file Excel.';
            $this->redirect('nilai');
            return;
        }
        $file = $_FILES['file_excel'];
        if (!in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), ['xlsx', 'xls'])) {
            $_SESSION['error'] = 'Format tidak valid.';
            $this->redirect('nilai');
            return;
        }
        $autoload = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($autoload)) {
            $_SESSION['error'] = 'PhpSpreadsheet not found.';
            $this->redirect('nilai');
            return;
        }
        require_once $autoload;
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file['tmp_name']);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file['tmp_name']);
        } catch (\Throwable $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            $this->redirect('nilai');
            return;
        }

        $db = \App\Core\Database::connect();
        $mapelIndex = $this->getMapelImportIndex($db);
        $imported = 0; $skipped = 0; $notFound = 0; $sheetValid = 0;

        try {
            $db->beginTransaction();
            foreach ($spreadsheet->getAllSheets() as $sheet) {
                $rows = $sheet->toArray(null, true, true, false);
                if (count($rows) < 2) continue;
                $headers = array_map(fn($h) => $this->normalizeImportHeader((string)$h), $rows[0]);
                $colMap = array_flip($headers);
                if (array_filter(['nisn', 'tahunajaran', 'kelas', 'semester'], fn($h) => !isset($colMap[$h]))) continue;
                $sheetValid++;
                for ($i = 1; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    $nisn = trim((string)($row[$colMap['nisn']] ?? ''));
                    $tahunAjaran = trim((string)($row[$colMap['tahunajaran']] ?? ''));
                    $kelas = trim((string)($row[$colMap['kelas']] ?? ''));
                    $semester = $this->normalizeImportSemester($kelas, $row[$colMap['semester']] ?? '');
                    if ($nisn === '' || $tahunAjaran === '' || $kelas === '' || $semester === 0) { $skipped++; continue; }
                    $idRiwayat = $this->findRiwayatForImport($db, $nisn, trim((string)($row[$colMap['nama']] ?? '')), $tahunAjaran, $kelas, $semester);
                    if ($idRiwayat <= 0) { $notFound++; continue; }
                    [$tingkat, $jurusan] = $this->inferTingkatJurusan($kelas);
                    $mapelGroup = $tingkat . '|' . strtoupper($jurusan);
                    $nilaiInput = []; $absenInput = []; $ekskulInput = []; $prestasiInput = [];
                    $hasAbs = $hasEks = $hasPres = false;
                    foreach ($headers as $idx => $key) {
                        $cell = $row[$idx] ?? '';
                        if (in_array($key, ['sakit', 'izin', 'alpa'])) { $absenInput[$key] = $cell; $hasAbs = true; }
                        elseif (isset($mapelIndex[$mapelGroup][$key])) { $nilaiInput[] = ['id_mapel' => $mapelIndex[$mapelGroup][$key], 'nilai_angka' => $cell]; }
                        elseif (preg_match('/^(ekskul|ekstrakurikuler)(\d*)$/', $key, $m)) { $ekskulInput[$m[2]?:1]['nama'] = $cell; $hasEks = true; }
                        elseif (preg_match('/^predikat(?:ekskul|ekstrakurikuler)?(\d*)$/', $key, $m)) { $ekskulInput[$m[1]?:1]['predikat'] = $cell; $hasEks = true; }
                        elseif (preg_match('/^prestasi(\d*)$/', $key, $m)) { $prestasiInput[$m[1]?:1]['nama'] = $cell; $hasPres = true; }
                        elseif (preg_match('/^tingkat(?:prestasi)?(\d*)$/', $key, $m)) { $prestasiInput[$m[1]?:1]['tingkat'] = $cell; $hasPres = true; }
                    }
                    $this->saveNilaiData($db, $idRiwayat, $nilaiInput, $absenInput, array_values($ekskulInput), array_values($prestasiInput), $hasEks, $hasPres, $hasAbs);
                    $groupMapelIds = array_unique(array_values($mapelIndex[$mapelGroup] ?? []));
                    $coveredIds = array_map(fn($n) => (int)$n['id_mapel'], $nilaiInput);
                    $delMissing = $db->prepare('DELETE FROM nilai WHERE id_riwayat = :r AND id_mapel = :m');
                    foreach ($groupMapelIds as $mid) if (!in_array($mid, $coveredIds)) $delMissing->execute([':r' => $idRiwayat, ':m' => $mid]);
                    $imported++;
                }
            }
            if ($sheetValid === 0) throw new \RuntimeException('Format tidak valid.');
            $db->commit();
            push_notif("{$imported} data diimport." . ($skipped?", {$skipped} dilewati":"") . ($notFound?", {$notFound} tidak ditemukan":""));
        } catch (\Throwable $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Gagal: ' . $e->getMessage();
        } finally {
            if (isset($spreadsheet)) $spreadsheet->disconnectWorksheets();
        }
        $this->redirect('nilai');
    }

    public function template(): void
    {
        require_login();
        $autoload = __DIR__ . '/../../vendor/autoload.php';
        if (!file_exists($autoload)) { $this->redirect('nilai'); return; }
        require_once $autoload;
        $db = \App\Core\Database::connect();
        $ta = trim($_GET['tahun_ajaran'] ?? '');
        $kls = trim($_GET['kelas'] ?? '');
        $semIn = trim($_GET['semester'] ?? '');
        if ($ta === '' || $kls === '' || $semIn === '') { $_SESSION['error'] = 'Lengkapi filter.'; $this->redirect('nilai'); return; }
        $semNum = (strtolower($semIn) === 'ganjil' || strtolower($semIn) === 'genap') ? RiwayatKelas::hitungSemester($kls, $semIn) : (int)$semIn;
        $stmtS = $db->prepare("SELECT s.nama, s.nisn FROM riwayat_kelas rk JOIN siswa s ON rk.id_siswa = s.id_siswa WHERE rk.tahun_ajaran = :ta AND rk.kelas = :kls AND rk.semester = :sem ORDER BY s.nama ASC");
        $stmtS->execute([':ta' => $ta, ':kls' => $kls, ':sem' => $semNum]);
        $siswa = $stmtS->fetchAll();
        if (empty($siswa)) { $_SESSION['error'] = 'Siswa tidak ditemukan.'; $this->redirect('nilai'); return; }
        [$ting, $jur] = $this->inferTingkatJurusan($kls);
        $order = MataPelajaran::orderPakemSql('nama_mapel');
        $stmtM = $db->prepare("SELECT kode_mapel FROM mata_pelajaran WHERE tingkat = :ting AND jurusan = :jur ORDER BY $order");
        $stmtM->execute([':ting' => $ting, ':jur' => $jur]);
        $mapels = $stmtM->fetchAll(\PDO::FETCH_COLUMN);
        $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $ss->getActiveSheet();
        $headers = array_merge(['Nama', 'NISN', 'Tahun Ajaran', 'Kelas', 'Semester'], $mapels, ['Sakit', 'Izin', 'Alpa'], ['Ekskul 1', 'Predikat 1', 'Ekskul 2', 'Predikat 2', 'Prestasi 1', 'Tingkat 1', 'Prestasi 2', 'Tingkat 2']);
        $sheet->fromArray($headers, null, 'A1');
        $row = 2; $semLab = ($semNum % 2 !== 0) ? 'Ganjil' : 'Genap';
        foreach ($siswa as $s) { $sheet->fromArray([$s['nama'], $s['nisn'], $ta, $kls, $semLab], null, 'A' . $row++); }
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Template_Nilai.xlsx"');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ss);
        $writer->save('php://output');
        $ss->disconnectWorksheets();
        exit;
    }

    public function cetakLeger(): void
    {
        require_login();
        date_default_timezone_set('Asia/Jakarta');
        $ta = trim($_GET['tahun_ajaran'] ?? ''); $kls = trim($_GET['kelas'] ?? ''); $sem = (int)($_GET['semester'] ?? 0);
        if ($ta === '' || $kls === '' || $sem === 0) { $this->redirect('nilai'); return; }
        $db = \App\Core\Database::connect();
        [$ting, $jur] = $this->inferTingkatJurusan($kls);
        $order = MataPelajaran::orderPakemSql('nama_mapel');
        $stmtM = $db->prepare("SELECT id_mapel, kode_mapel, nama_mapel FROM mata_pelajaran WHERE tingkat = :ting AND jurusan = :jur ORDER BY $order");
        $stmtM->execute([':ting' => $ting, ':jur' => $jur]);
        $mapels = $stmtM->fetchAll();
        $stmtS = $db->prepare("SELECT rk.id_riwayat, s.nama, s.nisn, s.nis FROM riwayat_kelas rk JOIN siswa s ON rk.id_siswa = s.id_siswa WHERE rk.tahun_ajaran = :ta AND rk.kelas = :kls AND rk.semester = :sem ORDER BY s.nama ASC");
        $stmtS->execute([':ta' => $ta, ':kls' => $kls, ':sem' => $sem]);
        $siswas = $stmtS->fetchAll();
        if (empty($siswas)) { $this->redirect('nilai'); return; }
        $leger = []; $hasEks = $hasPres = false;
        foreach ($siswas as $s) {
            $idR = $s['id_riwayat'];
            $stmtN = $db->prepare("SELECT id_mapel, nilai FROM nilai WHERE id_riwayat = :id"); $stmtN->execute([':id' => $idR]);
            $nMap = []; foreach ($stmtN->fetchAll() as $n) $nMap[$n['id_mapel']] = $n['nilai'];
            $stmtA = $db->prepare("SELECT sakit, izin, alpa FROM absensi WHERE id_riwayat = :id"); $stmtA->execute([':id' => $idR]);
            $abs = $stmtA->fetch() ?: ['sakit' => 0, 'izin' => 0, 'alpa' => 0];
            $stmtE = $db->prepare("SELECT nama_ekskul, predikat FROM ekstrakurikuler WHERE id_riwayat = :id"); $stmtE->execute([':id' => $idR]);
            $eks = $stmtE->fetchAll(); if ($eks) $hasEks = true;
            $stmtP = $db->prepare("SELECT nama_prestasi, tingkat FROM prestasi WHERE id_riwayat = :id"); $stmtP->execute([':id' => $idR]);
            $pre = $stmtP->fetchAll(); if ($pre) $hasPres = true;
            $tot = array_sum($nMap); $rat = count($mapels) > 0 ? $tot / count($mapels) : 0;
            $leger[] = ['nama' => $s['nama'], 'nisn' => $s['nisn'], 'nis' => $s['nis'], 'nilai' => $nMap, 'total' => $tot, 'rata' => $rat, 'absen' => $abs, 'ekskul' => $eks, 'prestasi' => $pre];
        }
        ob_start();
        $this->view('nilai/leger-pdf', [
            'tahunAjaran' => $ta, 'kelas' => $kls, 'semester' => str_contains(strtolower(RiwayatKelas::labelSemester($sem)), 'ganjil') ? 'Ganjil' : 'Genap',
            'waliKelas' => $_GET['wali_kelas'] ?? '', 'tipeNipWali' => $_GET['tipe_nip_wali'] ?? 'NIP.', 'nipWali' => $_GET['nip_wali'] ?? '',
            'kepsek' => $_GET['kepsek'] ?? '', 'tipeNipKepsek' => $_GET['tipe_nip_kepsek'] ?? 'NIP.', 'nipKepsek' => $_GET['nip_kepsek'] ?? '',
            'mapelList' => $mapels, 'dataLeger' => $leger, 'hasEkskul' => $hasEks, 'hasPrestasi' => $hasPres
        ]);
        $html = ob_get_clean();
        $opts = new \Dompdf\Options(); 
        $opts->set('isHtml5ParserEnabled', true); 
        $opts->set('isRemoteEnabled', true);
        
        $pdf = new \Dompdf\Dompdf($opts); 
        $pdf->loadHtml($html); 
        $pdf->setPaper('A4', 'landscape'); 
        $pdf->render();

        // --- NEW: Simpan Permanen & Catat ke DB ---
        $uploadDir = __DIR__ . '/../../public/uploads/leger/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $taClean  = str_replace('/', '-', $ta);
        $klsClean = str_replace(' ', '-', $kls);
        $semLabel = str_contains(strtolower(RiwayatKelas::labelSemester($sem)), 'ganjil') ? 'ganjil' : 'genap';
        $timestamp = date('d-m-Y_H-i-s');
        
        $filename = "Leger-Nilai_{$taClean}_{$klsClean}_{$semLabel}_{$timestamp}.pdf";
        $filePath = 'uploads/leger/' . $filename;

        // Cegah Duplikasi: Cek apakah laporan yang sama baru saja dibuat (dalam 10 detik terakhir)
        $dbCheck = \App\Core\Database::connect();
        $stmtCheck = $dbCheck->prepare("
            SELECT id_laporan FROM laporan 
            WHERE id_user = :id_user 
            AND jenis_laporan = 'leger' 
            AND komponen_laporan = :komponen 
            AND tanggal_buat >= DATE_SUB(NOW(), INTERVAL 10 SECOND)
            LIMIT 1
        ");
        $komponenJson = json_encode([
            'ta' => $ta,
            'kelas' => $kls,
            'sem' => $semLabel
        ]);
        $user = current_user();
        $idUser = $user['id_user'] ?? 0;

        $stmtCheck->execute([
            'id_user' => $idUser,
            'komponen' => $komponenJson
        ]);
        $isDuplicate = $stmtCheck->fetch();

        if (!$isDuplicate) {
            // Simpan ke disk
            file_put_contents($uploadDir . $filename, $pdf->output());

            // Simpan ke database laporan
            $stmtLog = $dbCheck->prepare("
                INSERT INTO laporan (id_user, jenis_laporan, orientasi, komponen_laporan, file_path, tanggal_buat)
                VALUES (:id_u, 'leger', 'landscape', :komponen, :path, NOW())
            ");
            $stmtLog->execute([
                ':id_u'     => $idUser,
                ':komponen' => $komponenJson,
                ':path'     => $filePath
            ]);
        }

        header('Content-Type: application/pdf'); 
        header('Content-Disposition: inline; filename="' . $filename . '"');
        echo $pdf->output(); 
        exit;
    }
}
