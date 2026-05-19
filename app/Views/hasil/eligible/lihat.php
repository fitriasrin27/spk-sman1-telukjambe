<?php
/**
 * @var int    $id
 * @var array  $batch
 * @var array  $hasil
 * @var int    $limit
 * @var int    $page
 * @var int    $total
 * @var int    $totalPages
 * @var int    $from
 * @var int    $to
 * @var int    $offset
 */

$bulanId = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
if (!function_exists('tglId')) {
    function tglId(string $datetime, array $bulan): string {
        $ts = strtotime($datetime);
        return date('d', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y, H:i', $ts);
    }
}
?>

<!-- JUDUL HALAMAN -->
<div class="page-header">
    <h2 class="page-title">Hasil Akhir Peringkat Eligible</h2>
</div>
<div class="title-separator mb-4"></div>

<!-- HASIL PERINGKAT -->
<div class="card border-0 shadow-sm overflow-hidden">
    <!-- Header Card: Info Hasil Peringkat -->
    <div class="card-header bg-white py-3 border-0">
        <!-- Baris 1: Judul & Tombol Navigasi -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-award text-warning fs-5"></i>
                <strong class="fs-6 text-dark">Hasil Perhitungan Peringkat Eligible</strong>
                <span class="badge rounded-pill px-2 py-1" style="background: #e0e7ff; color: #4338ca; font-weight: 600; font-size: 0.75rem;">
                    <?= e($batch['jurusan']) ?> &bull; <?= e($batch['tahun_ajaran']) ?>
                </span>
                <small class="text-muted" style="font-size: 0.75rem;">
                    Dihitung: <?= tglId($batch['tanggal_hitung'], $bulanId) ?>
                </small>
            </div>
            <!-- Tombol Kembali -->
            <div class="d-flex gap-2">
                <a href="<?= e(url('hasil/eligible')) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Riwayat
                </a>
            </div>
        </div>

        <hr class="my-3 text-muted opacity-25">

        <!-- Info Bobot Kriteria yang Digunakan -->
        <div class="mb-2 text-center">
            <strong class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Bobot Kriteria (Saat Perhitungan)</strong>
        </div>
        <div class="row g-2 justify-content-center mb-3">
            <?php 
            $bobotDisplay = [
                'C1' => ['Akademik', $batch['bobot_c1'] ?? 0],
                'C2' => ['Absensi', $batch['bobot_c2'] ?? 0],
                'C3' => ['Ekskul', $batch['bobot_c3'] ?? 0],
                'C4' => ['Prestasi', $batch['bobot_c4'] ?? 0],
            ];
            foreach ($bobotDisplay as $kode => $b): 
            ?>
                <!-- Info Item Bobot -->
                <div class="col-auto">
                    <div class="px-3 py-1 bg-light border rounded-pill d-flex align-items-center gap-2">
                        <span class="fw-bold text-primary small"><?= $kode ?></span>
                        <span class="text-muted small"><?= $b[0] ?>:</span>
                        <span class="fw-bold small"><?= number_format($b[1] * 100, 0) ?>%</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <hr class="my-3 text-muted opacity-25">

        <!-- Tombol Aksi Laporan -->
        <div class="mb-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <!-- Tombol Generate Laporan -->
                <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'tu'])): ?>
                <button type="button" class="btn btn-success btn-sm px-3 fw-medium shadow-sm" id="btnGenerateLaporan">
                    <i class="bi bi-file-earmark-text me-1"></i>Generate Laporan
                </button>
                <?php endif; ?>
                <div id="badgeContainer" class="d-flex align-items-center gap-2"></div>
            </div>
            <div id="rightActionContainer" class="d-flex align-items-center gap-2"></div>
        </div>

        <!-- Control Panel: Show Entries & Search -->
        <form method="GET" action="<?= e(url('')) ?>" id="formFilterHasil">
            <input type="hidden" name="url" value="hasil/eligible-lihat">
            <input type="hidden" name="id" value="<?= $id ?>">
            
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <!-- Limit Entries -->
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">Show</label>
                    <select name="limit" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                        <?php foreach ([10, 25, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="text-muted small mb-0">entries</label>
                </div>
                <!-- Input Pencarian -->
                <div style="min-width: 250px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="q" id="searchQueryInput" class="form-control border-start-0 ps-0" 
                               placeholder="Cari nama, NISN atau NIS..." autocomplete="off"
                               value="<?= e($search) ?>" autofocus>
                        <button type="submit" class="btn btn-primary btn-sm px-3 d-none">Cari</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Body Card: Tabel Hasil Peringkat -->
    <div class="card-body p-3 pt-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <!-- Header Tabel -->
                <thead class="bg-light text-center fw-bold text-muted">
                    <tr>
                        <th style="width: 70px;">Rank</th>
                        <th style="min-width: 250px;">Nama Lengkap</th>
                        <th style="width: 140px;">NISN</th>
                        <th style="width: 140px;">NIS</th>
                        <th style="width: 140px;">Nilai Preferensi</th>
                        <th style="width: 140px;">Total Nilai</th>
                        <th style="width: 140px;">Rata-Rata<br>Nilai</th>
                    </tr>
                </thead>
                <!-- Body Tabel -->
                <tbody class="text-dark">
                    <?php if (empty($hasil)): ?>
                        <!-- State Kosong -->
                        <tr><td colspan="7" class="text-center py-5 text-muted">Tidak ada data hasil untuk peringkat ini.</td></tr>
                    <?php else: ?>
                        <!-- Loop Data Siswa -->
                        <?php foreach ($hasil as $h): ?>
                            <tr class="text-center">
                                <!-- Kolom Peringkat -->
                                <td>
                                    <?php if ($h['ranking'] == 1): ?>
                                        <span class="rank-badge rank-1"><i class="bi bi-award-fill"></i> 1</span>
                                    <?php elseif ($h['ranking'] == 2): ?>
                                        <span class="rank-badge rank-2"><i class="bi bi-award-fill"></i> 2</span>
                                    <?php elseif ($h['ranking'] == 3): ?>
                                        <span class="rank-badge rank-3"><i class="bi bi-award-fill"></i> 3</span>
                                    <?php else: ?>
                                        <span><?= $h['ranking'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <!-- Info Detail Siswa -->
                                <td class="text-start ps-3"><?= e($h['nama']) ?></td>
                                <td><?= e($h['nisn']) ?></td>
                                <td><?= e($h['nis']) ?></td>
                                <td><?= number_format($h['nilai_preferensi'], 4) ?></td>
                                <td><?= number_format($h['c1_nilai_akademik'], 2, ',', '.') ?></td>
                                <td><?= number_format($h['rata_rata_akurat'] ?? $h['rata_rata_akademik'], 4) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Card: Pagination -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-3">
            <div class="text-muted small">
                Showing <?= $from ?> to <?= $to ?> of <?= $total ?> entries
            </div>
            <!-- Pagination Controls -->
            <?php if ($totalPages > 1): ?>
            <nav class="mx-auto">
                <?php $pageUrl = url('hasil/eligible-lihat') . "&id=$id&limit=$limit&q=" . urlencode($search); ?>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($pageUrl . '&page=' . ($page-1)) ?>">‹</a>
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
                        <a class="page-link" href="<?= e($pageUrl . '&page=' . ($page+1)) ?>">›</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
            <div style="width: 150px;" class="d-none d-md-block"></div>
        </div>
    </div>
</div>

<!-- DATA ATRIBUT BATCH (AJAX) -->
<div id="batchDataInfo" 
     data-batch-id="<?= $id ?>" 
     data-ta-clean="<?= str_replace(['/', ' '], '-', $batch['tahun_ajaran']) ?>" 
     data-jur-clean="<?= str_replace(['/', ' '], '-', $batch['jurusan']) ?>" 
     data-view-jurusan="<?= e($batch['jurusan']) ?>" 
     data-view-ta="<?= e($batch['tahun_ajaran']) ?>" 
     data-generate-url="<?= url('hasil/generate-pdf') ?>" 
     data-notif-add-url="<?= url('notif/add') ?>" 
     data-list-laporan-url="<?= url('laporan') ?>"></div>

<!-- MODALS & SCRIPTS -->
<?php include __DIR__ . '/modal-laporan.php'; ?>

<!-- STYLING CSS -->
<link rel="stylesheet" href="<?= e(asset('assets/css/pages/hasil.css')); ?>">

<!-- SCRIPT JS -->
<script src="<?= e(asset('assets/js/pages/hasil-eligible.js')); ?>"></script>
