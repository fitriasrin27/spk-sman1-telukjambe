<?php
/**
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

// Convert gambar ke base64 untuk keandalan Dompdf
function imageToBase64($path) {
    if (!file_exists($path)) return '';
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}
// Konversi gambar ke base64
$logoJabar = imageToBase64(__DIR__ . '/../../../public/assets/img/logo-pemprov-jabar.svg');
$logoSma = imageToBase64(__DIR__ . '/../../../public/assets/img/logo-sman1-nobg.png');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        <?php 
        $cssPath = __DIR__ . '/../../../public/assets/css/pages/nilai.css';
        if (file_exists($cssPath)) {
            echo file_get_contents($cssPath);
        }
        ?>
    </style>
</head>
<body class="leger-pdf-dompdf">
    <!-- Kop Surat -->
    <table class="kop-surat-pdf">
        <tr>
            <!-- Logo Jawa Barat -->
            <td class="logo-left">
                <?php if ($logoJabar): ?>
                    <img src="<?= $logoJabar ?>" width="55">
                <?php endif; ?>
            </td>
            <!-- Header Text -->
            <td class="header-text-pdf">
                <h3>PEMERINTAH DAERAH PROVINSI JAWA BARAT</h3>
                <h3>DINAS PENDIDIKAN</h3>
                <h3>CABANG DINAS PENDIDIKAN WILAYAH IV</h3>
                <h1>SEKOLAH MENENGAH ATAS NEGERI 1 TELUKJAMBE</h1>
                <p>Jl. HS. Ronggowaluyo, Desa Sirnabaya, Kec. Telukjambe Timur, Kab. Karawang 41361</p>
                <p>Website: www.sman1telukjambe.sch.id - Email: infosman1tj@gmail.com</p>
            </td>
            <!-- Logo SMAN 1 Telukjambe -->
            <td class="logo-right">
                <?php if ($logoSma): ?>
                    <img src="<?= $logoSma ?>" width="55">
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- Judul Leger -->
    <div class="report-title-pdf">LEGER NILAI SISWA</div>

    <!-- Info Siswa & Riwayat Terpilih -->
    <table class="info-table-pdf">
        <!-- Sisi kiri -->
        <tr>
            <td class="label">Tahun Ajaran</td>
            <td class="sep">:</td>
            <td style="width: 40%;"><?= e($tahunAjaran) ?></td>
            <td class="label">Semester</td>
            <td class="sep">:</td>
            <td><?= e($semester) ?></td>
        </tr>
        <!-- Sisi kanan -->
        <tr>
            <td class="label">Kelas / Jurusan</td>
            <td class="sep">:</td>
            <td><?= e($kelas) ?></td>
            <td class="label">Wali Kelas</td>
            <td class="sep">:</td>
            <td><?= e($waliKelas ?: '................................................') ?></td>
        </tr>
    </table>

    <table class="main-table-pdf">
        <!-- Header Tabel -->
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2" class="text-wrap-col" style="min-width: 130px;">Nama Lengkap</th>
                <th rowspan="2">NISN</th>
                <th rowspan="2">NIS</th>
                <th colspan="<?= count($mapelList) ?>">Mata Pelajaran (Nilai)</th>
                <th rowspan="2">Total Nilai</th>
                <th rowspan="2">Rata-Rata</th>
                <th colspan="3">Ketidakhadiran</th>
                <?php if ($hasEkskul): ?>
                    <th rowspan="2" class="text-wrap-col" style="min-width: 55px;">Ekskul</th>
                <?php endif; ?>
                <?php if ($hasPrestasi): ?>
                    <th rowspan="2" class="text-wrap-col" style="min-width: 75px;">Prestasi</th>
                <?php endif; ?>
            </tr>
            <tr>
                <?php foreach ($mapelList as $m): ?>
                    <th style="width: 20px;"><?= e($m['kode_mapel']) ?></th>
                <?php endforeach; ?>
                <th style="width: 20px;">S</th>
                <th style="width: 20px;">I</th>
                <th style="width: 20px;">A</th>
            </tr>
        </thead>
        <!-- Isi Tabel -->
        <tbody>
            <?php foreach ($dataLeger as $idx => $row): ?>
                <tr>
                    <!-- Nomor -->
                    <td><?= $idx + 1 ?></td>
                    <!-- Nama Lengkap -->
                    <td class="text-wrap-col text-start-pdf"><?= e($row['nama']) ?></td>
                    <!-- NISN -->
                    <td><?= e($row['nisn']) ?></td>
                    <!-- NIS -->
                    <td><?= e($row['nis']) ?></td>
                    <!-- Nilai Mata Pelajaran -->
                    <?php foreach ($mapelList as $m): ?>
                        <td><?= isset($row['nilai'][$m['id_mapel']]) ? number_format($row['nilai'][$m['id_mapel']], 0) : '-' ?></td>
                    <?php endforeach; ?>
                    <!-- Total Nilai -->
                    <td style="font-weight: bold;"><?= number_format($row['total'], 0) ?></td>
                    <!-- Rata-rata -->
                    <td style="font-weight: bold;"><?= number_format($row['rata'], 1) ?></td>
                    <!-- Absensi -->
                    <td><?= $row['absen']['sakit'] ?: 0 ?></td>
                    <td><?= $row['absen']['izin'] ?: 0 ?></td>
                    <td><?= $row['absen']['alpa'] ?: 0 ?></td>

                    <?php if ($hasEkskul): ?>
                        <!-- Ekskul -->
                        <td class="text-wrap-col text-start-pdf" style="font-size: 5pt; line-height: 1.0;">
                            <?php 
                            $ekskulTexts = [];
                            foreach ($row['ekskul'] as $e) {
                                $ekskulTexts[] = e($e['nama_ekskul']) . ' (' . e($e['predikat']) . ')';
                            }
                            echo implode(', ', $ekskulTexts);
                            ?>
                        </td>
                    <?php endif; ?>
                    <?php if ($hasPrestasi): ?>
                        <!-- Prestasi -->
                        <td class="text-wrap-col text-start-pdf" style="font-size: 5pt; line-height: 1.0;">
                            <?php 
                            $prestasiTexts = [];
                            foreach ($row['prestasi'] as $p) {
                                $prestasiTexts[] = e($p['nama_prestasi']) . ' (' . e(str_replace(['Kabupaten/Kota', 'Kabupaten / Kota'], 'Kab/Kota', $p['tingkat'])) . ')';
                            }
                            echo implode(', ', $prestasiTexts);
                            ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Footer -->
    <table class="footer-table-pdf">
        <tr>
            <!-- Kepala Sekolah -->
            <td>
                Mengetahui,<br>
                Kepala Sekolah
                <div class="sig-space"></div>
                <b>( <?= e($kepsek ?: '............................................') ?> )</b><br>
                <?= e($tipeNipKepsek) ?> <?= e($nipKepsek ?: '........................................') ?>
            </td>
            <td></td>
            <!-- Wali Kelas -->
            <td>
                Karawang, <?= date('d') ?> <?= getBulanIndo(date('m')) ?> <?= date('Y') ?><br>
                Wali Kelas
                <div class="sig-space"></div>
                <b>( <?= e($waliKelas ?: '............................................') ?> )</b><br>
                <?= e($tipeNipWali) ?> <?= e($nipWali ?: '........................................') ?>
            </td>
        </tr>
    </table>

</body>
</html>
