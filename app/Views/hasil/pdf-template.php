<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <style>
        <?= file_get_contents(__DIR__ . '/../../../public/assets/css/pages/pdf.css') ?>
    </style>
    <?php
    $bulanId = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $tglRaw = $ttd['tanggal'] ?? date('Y-m-d');
    
    // Hilangkan "Karawang, " jika sudah ada di awal string agar tidak double
    $tglRaw = str_replace('Karawang, ', '', $tglRaw);
    
    $tglFinal = $tglRaw;
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglRaw)) {
        $ex = explode('-', $tglRaw);
        $tglFinal = $ex[2] . ' ' . $bulanId[(int)$ex[1]] . ' ' . $ex[0];
    }
    ?>
</head>
<body>
    <!-- Kop Surat -->
    <div class="kop-surat" style="border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 15px;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 12%; text-align: left; border: none;">
                    <img src="<?= $logoJabar ?>" style="width: 70px;" alt="Logo Jabar">
                </td>
                <td style="width: 76%; text-align: center; border: none;">
                    <div style="font-size: 11pt; margin-bottom: 2px;">PEMERINTAH DAERAH PROVINSI JAWA BARAT</div>
                    <div style="font-size: 13pt; margin-bottom: 2px;">DINAS PENDIDIKAN</div>
                    <div style="font-size: 11pt; margin-bottom: 2px;">CABANG DINAS PENDIDIKAN WILAYAH IV</div>
                    <div style="font-size: 15pt; font-weight: bold; margin-bottom: 4px;">SEKOLAH MENENGAH ATAS NEGERI 1 TELUKJAMBE</div>
                    <div style="font-size: 8pt; font-style: italic;">Jl. HS. Ronggowaluyo, Desa Sirnabaya, Kec. Telukjambe Timur, Kab. Karawang 41361</div>
                    <div style="font-size: 8pt;">
                        Website: <span style="color: blue; text-decoration: underline;">www.sman1telukjambe.sch.id</span> &nbsp; 
                        Email: <span style="color: blue; text-decoration: underline;">infosman1tj@gmail.com</span>
                    </div>
                </td>
                <td style="width: 12%; text-align: right; border: none;">
                    <img src="<?= $logoSekolah ?>" style="width: 75px;" alt="Logo SMA">
                </td>
            </tr>
        </table>
    </div>

    <!-- Judul -->
    <div class="report-title" style="text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 20px; text-decoration: underline; text-transform: uppercase;">
        <?= $jenis === 'kelas' ? 'LAPORAN HASIL AKHIR PERINGKAT KELAS' : 'LAPORAN HASIL AKHIR PERINGKAT ELIGIBLE (SNBP)' ?>
    </div>

    <!-- Info Utama -->
    <table class="info-table" style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
        <?php if ($jenis === 'kelas'): ?>
            <tr>
                <td style="width: 18%; border: none;">Tahun Ajaran</td>
                <td style="width: 2%; border: none;">:</td>
                <td style="width: 30%; border: none;"><?= $batch['tahun_ajaran'] ?></td>
                <td style="width: 18%; border: none;">Semester</td>
                <td style="width: 2%; border: none;">:</td>
                <td style="width: 30%; border: none;"><?= $semesterLabel ?></td>
            </tr>
            <tr>
                <td style="border: none;">Kelas / Jurusan</td>
                <td style="border: none;">:</td>
                <td style="border: none;"><?= $batch['kelas'] ?></td>
                <td style="border: none;">Wali Kelas</td>
                <td style="border: none;">:</td>
                <td style="border: none;"><?= $ttd['walas_nama'] ?></td>
            </tr>
        <?php else: ?>
            <tr>
                <td style="width: 18%; border: none;">Tahun Ajaran</td>
                <td style="width: 2%; border: none;">:</td>
                <td style="width: 30%; border: none;"><?= $batch['tahun_ajaran'] ?></td>
                <td style="width: 18%; border: none;">Jurusan</td>
                <td style="width: 2%; border: none;">:</td>
                <td style="width: 30%; border: none; ">Kelas XII <?= $batch['jurusan']   ?></td>
            </tr>
            <tr>
                <td colspan="6" style="border: none; height: 5px;"></td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- Tabel Data -->
    <table class="data-table">
        <thead>
            <?php $rs = (in_array('c1c4', $komponen) || in_array('n1n4', $komponen)) ? 2 : 1; ?>
            <!-- Header Tabel -->
            <tr>
                <th rowspan="<?= $rs ?>" style="width: 30px;">Rank</th>
                <th rowspan="<?= $rs ?>" class="text-center">Nama Lengkap</th>
                <?php if (in_array('nisn', $komponen)): ?><th rowspan="<?= $rs ?>">NISN</th><?php endif; ?>
                <?php if (in_array('nis', $komponen)): ?><th rowspan="<?= $rs ?>">NIS</th><?php endif; ?>
                
                <?php if (in_array('c1c4', $komponen)): ?>
                    <th colspan="4">Kriteria (Raw)</th>
                <?php endif; ?>
                
                <?php if (in_array('n1n4', $komponen)): ?>
                    <th colspan="4">Normalisasi</th>
                <?php endif; ?>
                
                <?php if (in_array('preferensi', $komponen)): ?><th rowspan="<?= $rs ?>">Preferensi</th><?php endif; ?>
                <?php if (in_array('totalnilai', $komponen)): ?><th rowspan="<?= $rs ?>">Total Nilai</th><?php endif; ?>
                <?php if (in_array('ratarata', $komponen)): ?><th rowspan="<?= $rs ?>">Rata-Rata<br>Nilai</th><?php endif; ?>
            </tr>
            <?php if ($rs === 2): ?>
            <!-- Sub Header Tabel -->
            <tr>
                <?php if (in_array('c1c4', $komponen)): ?>
                    <th>C1</th><th>C2</th><th>C3</th><th>C4</th>
                <?php endif; ?>
                <?php if (in_array('n1n4', $komponen)): ?>
                    <th>N1</th><th>N2</th><th>N3</th><th>N4</th>
                <?php endif; ?>
            </tr>
            <?php endif; ?>
        </thead>
        <tbody>
            <?php 
            $kuota = $batch['kuota_eligible'] ?? 0;
            foreach ($hasil as $i => $h): 
                // Hitung total kolom untuk divider
                $totalKolom = 2;
                if (in_array('nisn', $komponen)) $totalKolom++;
                if (in_array('nis', $komponen)) $totalKolom++;
                if (in_array('c1c4', $komponen)) $totalKolom += 4;
                if (in_array('n1n4', $komponen)) $totalKolom += 4;
                if (in_array('preferensi', $komponen)) $totalKolom++;
                if (in_array('totalnilai', $komponen)) $totalKolom++;
                if (in_array('ratarata', $komponen)) $totalKolom++;

                // Divider kuota eligible
                if ($jenis === 'eligible' && in_array('status', $komponen) && $i == $kuota): ?>
                    <tr class="eligible-divider">
                        <td colspan="<?= $totalKolom ?>" style="padding: 5px; border-top: 2px solid #fbbf24; border-bottom: 2px solid #fbbf24;">
                            Batas Kuota — Peringkat <?= $kuota + 1 ?> ke bawah tidak mendapat kuota SNBP
                        </td>
                    </tr>
                <?php endif; 

                // Kelas untuk baris data
                $rowClass = '';
                if ($jenis === 'eligible' && in_array('status', $komponen)) {
                    $rowClass = ($i < $kuota) ? 'row-eligible' : 'row-not-eligible';
                }
            ?>
            <!-- Baris Data -->
                <tr class="<?= $rowClass ?>">
                    <td><?= $i + 1 ?></td>
                    <td class="text-left"><?= $h['nama'] ?></td>
                    <?php if (in_array('nisn', $komponen)): ?><td><?= $h['nisn'] ?></td><?php endif; ?>
                    <?php if (in_array('nis', $komponen)): ?><td><?= $h['nis'] ?></td><?php endif; ?>
                    
                    <?php if (in_array('c1c4', $komponen)): ?>
                        <td><?= number_format($h['c1_nilai_akademik'], 2) ?></td>
                        <td><?= $h['c2_absensi'] ?></td>
                        <td><?= $h['c3_ekskul'] ?></td>
                        <td><?= $h['c4_prestasi'] ?></td>
                    <?php endif; ?>
                    
                    <?php if (in_array('n1n4', $komponen)): ?>
                        <td><?= number_format($h['n_c1'], 4) ?></td>
                        <td><?= number_format($h['n_c2'], 4) ?></td>
                        <td><?= number_format($h['n_c3'], 4) ?></td>
                        <td><?= number_format($h['n_c4'], 4) ?></td>
                    <?php endif; ?>
                    
                    <?php if (in_array('preferensi', $komponen)): ?><td><?= number_format($h['nilai_preferensi'], 4) ?></td><?php endif; ?>
                    <?php if (in_array('totalnilai', $komponen)): ?><td><?= number_format($h['c1_nilai_akademik'], 2) ?></td><?php endif; ?>
                    <?php if (in_array('ratarata', $komponen)): ?><td><?= number_format($h['rata_rata_akurat'] ?? $h['rata_rata_akademik'], 2) ?></td><?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    <!-- Tanda Tangan Kepala Sekolah -->
                    <div class="sign-box">
                        Mengetahui,<br>Kepala Sekolah
                        <div class="sign-space"></div>
                        <strong>( <?= !empty(trim($ttd['kepsek_nama'] ?? '')) ? e($ttd['kepsek_nama']) : '..........................................' ?> )</strong><br>
                        <?= $ttd['kepsek_type'] ?? 'NIP.' ?> <?= !empty(trim($ttd['kepsek_id'] ?? '')) ? e($ttd['kepsek_id']) : '................................' ?>
                    </div>
                </td>
                <td></td>
                <td>
                    <!-- Tanda Tangan Wali Kelas/Guru BK -->
                    <div class="sign-box">
                        Karawang, <?= e($tglFinal) ?><br>
                        <?= ($jenis === 'kelas' ? 'Wali Kelas' : 'Guru BK') ?>
                        <div class="sign-space"></div>
                        <?php 
                            $namaSign = ($jenis === 'kelas' ? $ttd['walas_nama'] : $ttd['bk_nama']);
                            $idSign   = ($jenis === 'kelas' ? $ttd['walas_id'] : $ttd['bk_id']);
                            $idType   = ($jenis === 'kelas' ? ($ttd['walas_type'] ?? 'NIP.') : ($ttd['bk_type'] ?? 'NIP.'));
                        ?>
                        <strong>( <?= !empty(trim($namaSign)) ? e($namaSign) : '..........................................' ?> )</strong><br>
                        <?= $idType ?> <?= !empty(trim($idSign)) ? e($idSign) : '................................' ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
