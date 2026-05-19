<?php
/**
 * @var array  $batch
 * @var array  $rows
 * @var array  $bobot  ['C1'=>['kode_kriteria','nama_kriteria','bobot'], ...]
 */
?>

<?php 
$bulanId = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
function tglId(string $datetime, array $bulan): string {
    $ts = strtotime($datetime);
    return date('d', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y, H:i', $ts);
}
?>
<link rel="stylesheet" href="<?= e(asset('assets/css/pages/perhitungan.css')); ?>">

<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Perhitungan Peringkat Eligible (SNBP)</h2>
</div>

<!-- Card: Detail Perhitungan -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <!-- Header: Back & Title -->
        <div class="d-flex align-items-center mb-4">
            <!-- Tombol kembali -->
            <div style="flex: 1;">
                <?php 
                $backUrl = url('perhitungan/eligible') . 
                           '&tahun_ajaran=' . urlencode($riwayat['tahun_ajaran']) . 
                           '&jurusan=' . urlencode($riwayat['jurusan']) . 
                           '&id=' . $riwayat['id_perhitungan'];
                ?>
                <a href="<?= e($backUrl); ?>" class="btn btn-sm btn-outline-secondary px-3">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>
            <!-- Judul Perhitungan -->
            <div class="text-center" style="flex: 2;">
                <h4 class="mb-0 fw-bold">Detail Perhitungan SAW Eligible</h4>
                <!-- Informasi Perhitungan -->
                <div class="text-muted small d-flex justify-content-center align-items-center gap-1 mt-1">
                    <span>Angkatan <strong><?= e($riwayat['tahun_ajaran']) ?></strong></span>
                    <span class="text-secondary">&bull;</span>
                    <span>Jurusan <strong><?= e($riwayat['jurusan']) ?></strong></span>
                    <span class="text-secondary">&bull;</span>
                    <span>Dihitung: <?= tglId($riwayat['tanggal_hitung'], $bulanId) ?></span>
                </div>
            </div>
            <div style="flex: 1;"></div>
        </div>

        <hr class="opacity-25 mb-4">

        <!-- Bobot Kriteria -->
        <div class="mb-2 text-center">
            <strong class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Bobot Kriteria</strong>
        </div>

        <!-- Summary Bar: Bobot Kriteria -->
        <div class="row g-0 bg-light border rounded overflow-hidden">
            <!-- Looping Bobot Kriteria -->
            <?php foreach ($bobot as $kode => $b): ?>
                <div class="col-6 col-md-3 p-2 border-end">
                    <div class="d-flex justify-content-between align-items-center px-2 py-1">
                        <div>
                            <div class="fw-bold text-primary small mb-0" style="font-size: 1.1rem; line-height: 1.5;"><?= e($kode) ?></div>
                            <div class="text-muted" style="font-size: 0.75rem; line-height: 1.3;"><?= e($b['nama_kriteria']) ?></div>
                        </div>
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
                <!-- URL ke halaman hasil akhir -->
                <?php 
                $hasilAkhirUrl = url('hasil/eligible-lihat') . '&id=' . $riwayat['id_perhitungan'];
                ?>
                <!-- Tombol Lihat Hasil Akhir -->
                <a href="<?= e($hasilAkhirUrl) ?>" 
                   class="btn btn-outline-success btn-sm px-3 fw-medium shadow-sm">
                    <i class="bi bi-bar-chart-line me-1"></i>Lihat Hasil Akhir
                </a>
            </div>

            <!-- Filter Entries & Search -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <!-- Filter Entries -->
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">Show</label>
                    <!-- Form Filter Entries -->
                    <form method="GET" action="<?= e(url('')) ?>" id="formLimit">
                        <input type="hidden" name="url" value="perhitungan/eligible-detail">
                        <input type="hidden" name="tahun_ajaran" value="<?= e($riwayat['tahun_ajaran']) ?>">
                        <input type="hidden" name="jurusan" value="<?= e($riwayat['jurusan']) ?>">
                        <input type="hidden" name="id" value="<?= e($riwayat['id_perhitungan']) ?>">
                        <select name="limit" class="form-select form-select-sm d-inline-block w-auto"
                                onchange="document.getElementById('formLimit').submit()">
                            <?php foreach ([10, 25, 50, 100] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <label class="text-muted small mb-0">entries</label>
                </div>

                <!-- Form Search -->
                <div style="min-width: 250px;">
                    <form method="GET" action="<?= e(url('')) ?>">
                        <input type="hidden" name="url" value="perhitungan/eligible-detail">
                        <input type="hidden" name="tahun_ajaran" value="<?= e($riwayat['tahun_ajaran']) ?>">
                        <input type="hidden" name="jurusan" value="<?= e($riwayat['jurusan']) ?>">
                        <input type="hidden" name="id" value="<?= e($riwayat['id_perhitungan']) ?>">
                        <input type="hidden" name="limit" value="<?= e($limit) ?>">
                        
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
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
                    <!-- Baris 1: Rank & Nama Lengkap -->
                    <tr>
                        <th rowspan="2" class="align-middle" style="width:45px;">Rank</th>
                        <th rowspan="2" class="align-middle" style="min-width:200px;">Nama Lengkap</th>
                        <th colspan="4" class="table-warning" style="box-shadow: -1px 0 0 0 #dee2e6;">Nilai Kriteria (Total 5 Semester)</th>
                        <th colspan="4" class="table-success">Nilai Normalisasi</th>
                        <th rowspan="2" class="align-middle table-primary">Nilai<br>Preferensi (Vi)</th>
                        <th rowspan="2" class="align-middle table-info">Rata-Rata<br>Akademik</th>
                    </tr>
                    <!-- Baris 2: Nilai Kriteria (Akademik, Absensi, Ekskul, Prestasi) -->
                    <tr>
                        <th class="table-warning" style="box-shadow: -1px 0 0 0 #dee2e6;">Akademik</th>
                        <th class="table-warning">Absensi</th>
                        <th class="table-warning">Ekskul</th>
                        <th class="table-warning">Prestasi</th>
                        <th class="table-success">Akademik</th>
                        <th class="table-success">Absensi</th>
                        <th class="table-success">Ekskul</th>
                        <th class="table-success">Prestasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($rows as $h): 
                        // Hitung Nilai Preferensi (Vi)
                        $v1 = $h['n_c1'] * (($bobot['C1']['bobot'] ?? 0));
                        $v2 = $h['n_c2'] * (($bobot['C2']['bobot'] ?? 0));
                        $v3 = $h['n_c3'] * (($bobot['C3']['bobot'] ?? 0));
                        $v4 = $h['n_c4'] * (($bobot['C4']['bobot'] ?? 0));
                    ?>
                        <!-- Baris Data -->
                        <tr>
                            <td class="text-center">
                                <?php if ($h['ranking'] <= 3): ?>
                                    <span class="badge fw-normal <?= $h['ranking'] == 1 ? 'bg-warning text-light' : ($h['ranking'] == 2 ? 'bg-secondary' : 'bg-bronze') ?>">
                                        <i class="bi bi-award me-1"></i><?= $h['ranking'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-dark small"><?= $h['ranking']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-start"><?= e($h['nama']); ?></td>
                            
                            <!-- Nilai Raw -->
                            <td class="text-center bg-warning bg-opacity-10"><?= number_format($h['c1_nilai_akademik'], 2); ?></td>
                            <td class="text-center bg-warning bg-opacity-10"><?= number_format($h['c2_absensi'], 0); ?></td>
                            <td class="text-center bg-warning bg-opacity-10"><?= number_format($h['c3_ekskul'], 0); ?></td>
                            <td class="text-center bg-warning bg-opacity-10"><?= number_format($h['c4_prestasi'], 0); ?></td>
                            
                            <!-- Normalisasi + Tooltips -->
                            <td class="text-center bg-success bg-opacity-10" title="<?= number_format($h['n_c1'], 4) ?> × <?= (($bobot['C1']['bobot'] ?? 0)*100) ?>% = <?= number_format($v1, 6) ?>">
                                <?= number_format($h['n_c1'], 4); ?>
                            </td>
                            <td class="text-center bg-success bg-opacity-10" title="<?= number_format($h['n_c2'], 4) ?> × <?= (($bobot['C2']['bobot'] ?? 0)*100) ?>% = <?= number_format($v2, 6) ?>">
                                <?= number_format($h['n_c2'], 4); ?>
                            </td>
                            <td class="text-center bg-success bg-opacity-10" title="<?= number_format($h['n_c3'], 4) ?> × <?= (($bobot['C3']['bobot'] ?? 0)*100) ?>% = <?= number_format($v3, 6) ?>">
                                <?= number_format($h['n_c3'], 4); ?>
                            </td>
                            <td class="text-center bg-success bg-opacity-10" title="<?= number_format($h['n_c4'], 4) ?> × <?= (($bobot['C4']['bobot'] ?? 0)*100) ?>% = <?= number_format($v4, 6) ?>">
                                <?= number_format($h['n_c4'], 4); ?>
                            </td>
                            
                            <!-- Preferensi -->
                            <td class="text-center fw-bold text-primary bg-primary bg-opacity-10" title="<?= number_format($v1, 6) ?> + <?= number_format($v2, 6) ?> + <?= number_format($v3, 6) ?> + <?= number_format($v4, 6) ?> = <?= number_format($h['nilai_preferensi'], 6) ?>">
                                <?= number_format($h['nilai_preferensi'], 6); ?>
                            </td>

                            <!-- Rata-Rata Baru -->
                            <td class="text-center bg-info bg-opacity-10">
                                <?= number_format($h['rata_rata_akademik'], 4); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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
                <?php $baseUrlDetail = url('perhitungan/eligible-detail') . '&id=' . $riwayat['id_perhitungan'] . '&q=' . urlencode($search); ?>
                <ul class="pagination pagination-sm mb-0">
                    <!-- Pagination -->
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($baseUrlDetail . '&page=' . ($page - 1) . '&limit=' . $limit) ?>">‹</a>
                    </li>
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <?php if ($p === 1 || $p === $totalPages || abs($p - $page) <= 1): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= e($baseUrlDetail . '&page=' . $p . '&limit=' . $limit) ?>"><?= $p ?></a>
                            </li>
                        <?php elseif (abs($p - $page) === 2): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($baseUrlDetail . '&page=' . ($page + 1) . '&limit=' . $limit) ?>">›</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= e(asset('assets/js/pages/perhitungan-eligible.js')); ?>"></script>
