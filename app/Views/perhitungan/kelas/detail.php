<?php
/**
 * @var array  $batch
 * @var array  $rows
 * @var array  $bobot  ['C1'=>['kode_kriteria','nama_kriteria','bobot'], ...]
 */

// Label Semester
$labelSemester = [
    1 => 'Ganjil',  2 => 'Genap',
    3 => 'Ganjil', 4 => 'Genap',
    5 => 'Ganjil', 6 => 'Genap',
];

// Bulan
$bulanId = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

// Tgl Id
function tglId(string $datetime, array $bulan): string {
    $ts = strtotime($datetime);
    return date('d', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y, H:i', $ts);
}

$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $limit, $total);
$baseUrl = url('perhitungan/detail-kelas') . '&id=' . $batch['id_perhitungan'] . '&limit=' . $limit;
?>
<link rel="stylesheet" href="<?= e(asset('assets/css/pages/perhitungan.css')); ?>">

<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Perhitungan Peringkat Kelas</h2>
</div>

<!-- Detail Perhitungan Kelas -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <!-- Header: Back & Title -->
        <div class="d-flex align-items-center mb-4">
            <!-- Tombol Kembali -->
            <div style="flex: 1;">
                <?php 
                $backUrl = url('perhitungan/kelas') . 
                           '&tahun_ajaran=' . urlencode($batch['tahun_ajaran']) . 
                           '&kelas=' . urlencode($batch['kelas']) . 
                           '&semester=' . ($batch['semester_target'] % 2 === 1 ? 'ganjil' : 'genap') . 
                           '&id=' . $batch['id_perhitungan'];
                ?>
                <!-- Link Kembali -->
                <a href="<?= e($backUrl); ?>" class="btn btn-sm btn-outline-secondary px-3">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
            <!-- Judul Perhitungan -->
            <div class="text-center" style="flex: 2;">
                <h4 class="mb-0 fw-bold">Detail Perhitungan SAW Kelas</h4>
                <div class="text-muted small d-flex justify-content-center align-items-center gap-1 mt-1">
                    <!-- Info Detail -->
                    <span>Kelas <strong><?= e($batch['kelas']) ?></strong></span>
                    <span class="text-secondary">&bull;</span>
                    <span>Semester <?= e($labelSemester[$batch['semester_target']] ?? 'Sem '.$batch['semester_target']) ?></span>
                    <span class="text-secondary">&bull;</span>
                    <span><?= e($batch['tahun_ajaran']) ?></span>
                    <span class="text-secondary">&bull;</span>
                    <span>Dihitung: <?= tglId($batch['tanggal_hitung'], $bulanId) ?></span>
                </div>
            </div>
            <div style="flex: 1;"></div> <!-- Spacer seimbang -->
        </div>

        <hr class="opacity-25 mb-4">

        <!-- Judul Bobot Kriteria -->
        <div class="mb-2 text-center">
            <strong class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Bobot Kriteria</strong>
        </div>

        <!-- Summary Bar: Bobot Kriteria -->
        <div class="row g-0 bg-light border rounded overflow-hidden">
            <?php foreach ($bobot as $kode => $b): ?>
                <div class="col-6 col-md-3 p-2 <?= $kode !== 'C4' ? 'border-end' : '' ?>">
                    <!-- Info Bobot Kriteria -->
                    <div class="d-flex justify-content-between align-items-center px-2 py-1">
                        <div>
                            <div class="fw-bold text-primary small mb-0" style="font-size: 1.1rem; line-height: 1.5;"><?= e($kode) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem; line-height: 1.3;"><?= e($b['nama_kriteria']) ?></div>
                        </div>
                        <!-- Bobot -->
                        <div class="fw-bold text-dark fs-5" style="line-height: 2;">
                            <?= number_format($b['bobot'] * 100, 0) ?>%
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Tabel Detail Lengkap -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Toolbar: Button on top, Entries & Search below -->
        <div class="mb-3">
            <!-- Tombol Lihat Hasil Akhir -->
            <div class="d-flex justify-content-end mb-2">
                <?php 
                $hasilAkhirUrl = url('hasil/kelas-lihat') . '&id=' . $batch['id_perhitungan'];
                ?>
                <a href="<?= e($hasilAkhirUrl) ?>" 
                   class="btn btn-outline-success btn-sm px-3 fw-medium shadow-sm">
                    <i class="bi bi-bar-chart-line me-1"></i>Lihat Hasil Akhir
                </a>
            </div>

            <!-- Info Show Entries -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">Show</label>
                    <form method="GET" action="<?= e(url('')) ?>" id="formLimit">
                        <input type="hidden" name="url" value="perhitungan/detail-kelas">
                        <input type="hidden" name="id" value="<?= e($batch['id_perhitungan']) ?>">
                        <select name="limit" class="form-select form-select-sm d-inline-block w-auto"
                                onchange="document.getElementById('formLimit').submit()">
                            <?php foreach ([10, 25, 50, 100] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <!-- Info Entries -->
                    <label class="text-muted small mb-0">entries</label>
                </div>

                <!-- Form Search -->
                <div style="min-width: 250px;">
                    <!-- Form Search -->
                    <form method="GET" action="<?= e(url('')) ?>">
                        <input type="hidden" name="url" value="perhitungan/detail-kelas">
                        <input type="hidden" name="id" value="<?= e($batch['id_perhitungan']) ?>">
                        <input type="hidden" name="limit" value="<?= e($limit) ?>">
                        <!-- Input Search -->
                        <div class="input-group input-group-sm">
                            <!-- Icon Search -->
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <!-- Input Search -->
                            <input type="text" name="q" id="searchQueryInput" class="form-control border-start-0 ps-0" 
                                   placeholder="Cari nama, NISN atau NIS..." autocomplete="off"
                                   value="<?= e($search) ?>" autofocus>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Tabel Detail Lengkap -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 small">
                <!-- Header Tabel -->
                <thead class="table-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle" style="width:45px;">Rank</th>
                        <th rowspan="2" class="align-middle" style="min-width:200px;">Nama Lengkap</th>
                        <th colspan="4" class="table-warning" style="box-shadow: -1px 0 0 0 #dee2e6;">Nilai Kriteria (Raw)</th>
                        <th colspan="4" class="table-success">Nilai Normalisasi</th>
                        <th rowspan="2" class="align-middle table-primary">Nilai<br>Preferensi (Vi)</th>
                        <th rowspan="2" class="align-middle">Rata-Rata<br>Nilai</th>
                    </tr>
                    <tr>
                        <th class="table-warning" style="box-shadow: -1px 0 0 0 #dee2e6;">Nilai</th>
                        <th class="table-warning">Absensi</th>
                        <th class="table-warning">Ekskul</th>
                        <th class="table-warning">Prestasi</th>
                        <th class="table-success">Nilai</th>
                        <th class="table-success">Absensi</th>
                        <th class="table-success">Ekskul</th>
                        <th class="table-success">Prestasi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data Tabel -->
                    <?php if (!empty($rows)): ?>
                        <!-- Looping Data -->
                        <?php foreach ($rows as $row): 
                            // Hitung kontribusi masing-masing untuk tooltip
                            $w1 = $bobot['C1']['bobot'] ?? 0;
                            $w2 = $bobot['C2']['bobot'] ?? 0;
                            $w3 = $bobot['C3']['bobot'] ?? 0;
                            $w4 = $bobot['C4']['bobot'] ?? 0;

                            $v1 = $row['n_c1'] * $w1;
                            $v2 = $row['n_c2'] * $w2;
                            $v3 = $row['n_c3'] * $w3;
                            $v4 = $row['n_c4'] * $w4;
                        ?>
                            <tr>
                                <!-- Peringkat -->
                                <td class="text-center">
                                    <!-- Tampilkan Badge Peringkat -->
                                    <?php if ($row['ranking'] <= 3): ?>
                                        <span class="badge fw-normal <?= ['bg-warning text-light','bg-secondary','badge-bronze'][($row['ranking']-1)] ?>">
                                            <i class="bi bi-award me-1"></i><?= $row['ranking'] ?>
                                        </span>
                                    <!-- Tampilkan Peringkat -->
                                    <?php else: ?>
                                        <span class="small"><?= $row['ranking'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <!-- Nama Siswa -->
                                <td><?= e($row['nama']) ?></td>
                                <!-- Nilai Raw -->
                                <td class="text-center bg-warning bg-opacity-10"><?= number_format($row['c1_nilai_akademik'], 2) ?></td>
                                <td class="text-center bg-warning bg-opacity-10"><?= number_format($row['c2_absensi'], 0) ?></td>
                                <td class="text-center bg-warning bg-opacity-10"><?= number_format($row['c3_ekskul'], 0) ?></td>
                                <td class="text-center bg-warning bg-opacity-10"><?= number_format($row['c4_prestasi'], 0) ?></td>
                                <!-- Normalisasi + Tooltip Transparansi -->
                                <td class="text-center bg-success bg-opacity-10" title="<?= number_format($row['n_c1'], 4) ?> × <?= ($w1*100) ?>% = <?= number_format($v1, 6) ?>">
                                    <?= number_format($row['n_c1'], 4) ?>
                                </td>
                                <td class="text-center bg-success bg-opacity-10" title="<?= number_format($row['n_c2'], 4) ?> × <?= ($w2*100) ?>% = <?= number_format($v2, 6) ?>">
                                    <?= number_format($row['n_c2'], 4) ?>
                                </td>
                                <td class="text-center bg-success bg-opacity-10" title="<?= number_format($row['n_c3'], 4) ?> × <?= ($w3*100) ?>% = <?= number_format($v3, 6) ?>">
                                    <?= number_format($row['n_c3'], 4) ?>
                                </td>
                                <td class="text-center bg-success bg-opacity-10" title="<?= number_format($row['n_c4'], 4) ?> × <?= ($w4*100) ?>% = <?= number_format($v4, 6) ?>">
                                    <?= number_format($row['n_c4'], 4) ?>
                                </td>
                                <!-- Preferensi + Tooltip Total -->
                                <td class="text-center fw-bold text-primary bg-primary bg-opacity-10" title="<?= number_format($v1, 6) ?> + <?= number_format($v2, 6) ?> + <?= number_format($v3, 6) ?> + <?= number_format($v4, 6) ?> = <?= number_format($row['nilai_preferensi'], 6) ?>">
                                    <?= number_format($row['nilai_preferensi'], 6) ?>
                                </td>
                                <!-- Rata-rata -->
                                <td class="text-center bg-info bg-opacity-10"><?= number_format($row['rata_rata_akurat'] ?? $row['rata_rata_akademik'], 4) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="17" class="text-center text-muted py-5">Tidak ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer tabel: info kiri, pagination tengah -->
        <?php if ($total > 0): ?>
        <div class="d-flex align-items-center mt-3" style="position: relative; min-height: 32px;">
            <!-- Kiri: info entries -->
            <div class="text-muted small">
                Show <?= $from ?> to <?= $to ?> of <?= $total ?> entries
            </div>
            <!-- Tengah: pagination (benar-benar di tengah) -->
            <?php if ($totalPages > 1): ?>
            <nav style="position: absolute; left: 50%; transform: translateX(-50%);">
                <?php $pageUrl = url('perhitungan/kelas-detail') . "&id=" . $batch['id_perhitungan'] . "&limit=$limit&q=" . urlencode($search); ?>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($pageUrl . '&page=' . ($page - 1)) ?>">‹</a>
                    </li>
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <?php if ($p === 1 || $p === $totalPages || abs($p - $page) <= 1): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= e($pageUrl . '&page=' . $p) ?>"><?= $p ?></a>
                            </li>
                        <?php elseif (abs($p - $page) === 2): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($pageUrl . '&page=' . ($page + 1)) ?>">›</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= e(asset('assets/js/pages/perhitungan-kelas.js')); ?>"></script>
