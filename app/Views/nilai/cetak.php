<?php
/**
 * @var string $title
 * @var string $tahunAjaran
 * @var string $kelas
 * @var int    $semester
 * @var array  $mapelList
 * @var array  $dataLeger
 * @var bool   $hasEkskul
 * @var bool   $hasPrestasi
 */

use App\Models\RiwayatKelas;

// Helper Nama Bulan Indonesia
function getBulanIndo($bulan) {
    $map = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $map[(int)$bulan] ?? '';
}

$namaSemester = RiwayatKelas::labelSemester($semester);
$timestamp = date('d-m-Y_H-i');
$filename = "Leger_Nilai_" . str_replace(['/', ' '], '-', $kelas) . "_" . str_replace(['/', ' '], '-', $tahunAjaran) . "_" . $namaSemester . "_" . $timestamp;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= e($filename) ?></title>
    <!-- Bootstrap Icons for Toolbar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= e(asset('assets/css/pages/nilai.css')); ?>">
</head>
<body class="leger-preview">
    <!-- Toolbar Preview -->
    <div class="pdf-toolbar no-print">
        <!-- Judul -->
        <div class="filename">
            <i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i>
            <?= e($filename) ?>.pdf
        </div>
        <!-- Tombol -->
        <div class="actions">
            <button onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
            <button onclick="window.print()">
                <i class="bi bi-download"></i> Simpan PDF
            </button>
            <button onclick="window.close()">
                <i class="bi bi-x-lg"></i> Tutup
            </button>
        </div>
    </div>

    <!-- Page Content -->
    <div class="pdf-container">
        <div class="page">
            <!-- Kop Surat -->
            <div class="kop-surat">
                <!-- Logo Jabar -->
                <img src="<?= asset('img/logo-pemprov-jabar.svg') ?>" class="logo" alt="Logo Jabar">

                <!-- Nama Instansi -->
                <div class="header-text">
                    <h3>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h3>
                    <h2>DINAS PENDIDIKAN</h2>
                    <h3>CABANG DINAS PENDIDIKAN WILAYAH IV</h3>
                    <h1>SEKOLAH MENENGAH ATAS NEGERI 1 TELUKJAMBE</h1>
                    <p>Jl. HS. Ronggowaluyo, Desa Sirnabaya, Kec. Telukjambe Timur, Kab. Karawang 41361</p>
                    <!-- Link -->
                    <div class="links">
                        Website: <span style="color: blue; text-decoration: underline;">www.sman1telukjambe.sch.id</span> &nbsp; 
                        Email: <span style="color: blue; text-decoration: underline;">infosman1tj@gmail.com</span>
                    </div>
                </div>
                <!-- Logo Sekolah -->
                <img src="<?= asset('img/logo-sman1-nobg.png') ?>" class="logo" alt="Logo SMA">
            </div>

            <!-- Judul Nama File -->
            <div class="report-title">LEGER NILAI SISWA</div>

            <!-- Info Grid -->
            <table class="info-grid">
                <!-- baris kiri -->
                <tr>
                    <td class="label">Tahun Ajaran</td>
                    <td class="sep">:</td>
                    <td style="width: 40%;"><?= e($tahunAjaran) ?></td>
                    <td class="label">Semester</td>
                    <td class="sep">:</td>
                    <td><?= e($namaSemester) ?></td>
                </tr>
                <!-- baris kanan -->
                <tr>
                    <td class="label">Kelas / Jurusan</td>
                    <td class="sep">:</td>
                    <td><?= e($kelas) ?></td>
                    <td class="label">Wali Kelas</td>
                    <td class="sep">:</td>
                    <td><?= e($wali_kelas ?? '(Wali Kelas Belum Diatur)') ?></td>
                </tr>
            </table>

            <!-- Tabel Nilai -->
            <table class="main-table">
                <!-- Header Tabel -->
                <thead>
                    <!-- baris pertama -->
                    <tr>
                        <th rowspan="2" class="col-no">No</th>
                        <th rowspan="2" class="col-nama">Nama Lengkap</th>
                        <th rowspan="2" class="col-id">NISN</th>
                        <th rowspan="2" class="col-id">NIS</th>
                        <th colspan="<?= count($mapelList) ?>">Mata Pelajaran (Nilai)</th>
                        <th rowspan="2" class="col-total">Total</th>
                        <th rowspan="2" class="col-rata">Rata</th>
                        <th colspan="3" style="width: 60px;">Absensi</th>
                        <?php if ($hasEkskul): ?>
                            <th rowspan="2" class="col-extra">Ekstrakurikuler</th>
                        <?php endif; ?>
                        <?php if ($hasPrestasi): ?>
                            <th rowspan="2" class="col-extra">Prestasi</th>
                        <?php endif; ?>
                    </tr>

                    <!-- baris kedua -->
                    <tr>
                        <?php foreach ($mapelList as $m): ?>
                            <th class="col-score" title="<?= e($m['nama_mapel']) ?>"><?= e($m['kode_mapel']) ?></th>
                        <?php endforeach; ?>
                        <th class="col-absen">S</th>
                        <th class="col-absen">I</th>
                        <th class="col-absen">A</th>
                    </tr>
                </thead>

                <!-- Isi Tabel -->
                <tbody>
                    <!-- baris data -->
                    <?php foreach ($dataLeger as $idx => $row): ?>
                        <tr>
                            <!-- No, Nama, NISN, NIS -->
                            <td><?= $idx + 1 ?></td>
                            <td class="text-start"><?= e($row['nama']) ?></td>
                            <td><?= e($row['nisn']) ?></td>
                            <td><?= e($row['nis']) ?></td>
                            <!-- Nilai per Mapel -->
                            <?php foreach ($mapelList as $m): ?>
                                <td><?= isset($row['nilai'][$m['id_mapel']]) ? number_format($row['nilai'][$m['id_mapel']], 0) : '-' ?></td>
                            <?php endforeach; ?>

                            <!-- Total & Rata-Rata -->
                            <td><?= number_format($row['total'], 0) ?></td>
                            <td><?= number_format($row['rata'], 1) ?></td>

                            <!-- Absensi -->
                            <td><?= $row['absen']['sakit'] ?: 0 ?></td>
                            <td><?= $row['absen']['izin'] ?: 0 ?></td>
                            <td><?= $row['absen']['alpa'] ?: 0 ?></td>

                            <!-- Ekstrakurikuler -->
                            <?php if ($hasEkskul): ?>
                                <td class="text-start" style="font-size: 5.5pt; line-height: 1.0;">
                                    <?php 
                                    $ekskulTexts = [];
                                    foreach ($row['ekskul'] as $e) {
                                        $ekskulTexts[] = e($e['nama_ekskul']) . ' (' . e($e['predikat']) . ')';
                                    }
                                    echo implode(', ', $ekskulTexts);
                                    ?>
                                </td>
                            <?php endif; ?>
                            <!-- Prestasi -->
                            <?php if ($hasPrestasi): ?>
                                <td class="text-start" style="font-size: 5.5pt; line-height: 1.0;">
                                    <?php 
                                    $prestasiTexts = [];
                                    foreach ($row['prestasi'] as $p) {
                                        $prestasiTexts[] = e($p['nama_prestasi']) . ' (' . e($p['tingkat']) . ')';
                                    }
                                    echo implode(', ', $prestasiTexts);
                                    ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Footer / Tanda Tangan -->
            <div class="footer">
                <table class="footer-table">
                    <tr>
                        <!-- Kepala Sekolah -->
                        <td>
                            Mengetahui,<br>
                            Kepala Sekolah
                            <div class="signature-name">( <?= !empty(trim($nama_kepsek ?? '')) ? e($nama_kepsek) : '..........................................' ?> )</div>
                            <div class="signature-id">NIP. <?= !empty(trim($nip_kepsek ?? '')) ? e($nip_kepsek) : '................................' ?></div>
                        </td>
                        <td></td>
                        <!-- Wali Kelas -->
                        <td>
                            Karawang, <?= e($tglFinal) ?><br>
                            Wali Kelas
                            <div class="signature-name">( <?= !empty(trim($wali_kelas ?? '')) ? e($wali_kelas) : '..........................................' ?> )</div>
                            <div class="signature-id">NIP. <?= !empty(trim($nip_wali ?? '')) ? e($nip_wali) : '................................' ?></div>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>

    <script>
        // Atur judul document untuk nama file saat disimpan
        document.title = "<?= e($filename) ?>"; //Nama File = Leger-Nilai_TA_KelasJurusan_Semester_Tanggal_Waktu
    </script>
</body>
</html>
