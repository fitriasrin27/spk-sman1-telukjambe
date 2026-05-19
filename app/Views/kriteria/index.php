<?php
/**
 * @var array $kriteria
 * @var int   $total
 * @var int   $page
 * @var int   $limit
 * @var int   $totalPages
 * @var int   $offset
 * @var string|null $error
 * @var float $totalBobot
 */

$baseUrl = url('kriteria');
$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $limit, $total);
$sisaBobot = max(0, 1.00 - $totalBobot);
$isBobotFull = round($totalBobot, 2) >= 1.00;
?>
<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Kriteria & Bobot</h2>
</div>

<!-- Flash Message -->
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
    <!-- Tombol Tambah Kriteria -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKriteria" <?= $isBobotFull ? 'disabled title="Kuota bobot sudah habis"' : '' ?>>
        <i class="bi bi-plus-lg me-1"></i>Tambah Kriteria
    </button>
    <!-- Info Sisa Kuota -->
    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded" style="background: <?= $isBobotFull ? '#d1fae5' : '#fffbeb' ?>; border: 1px solid <?= $isBobotFull ? '#10b981' : '#f59e0b' ?>;">
        <i class="bi <?= $isBobotFull ? 'bi-check-circle-fill text-success' : 'bi-info-circle-fill text-warning' ?> fs-5"></i>
        <div class="d-flex flex-column" style="line-height: 1.2;">
            <span class="fw-bold" style="color: <?= $isBobotFull ? '#047857' : '#b45309' ?>; font-size: 0.85rem;">Total Bobot: <?= number_format($totalBobot, 2) ?> / 1.00</span>
            <span class="small" style="color: <?= $isBobotFull ? '#047857' : '#b45309' ?>; font-size: 0.7rem; opacity: 0.8;">Sisa Kuota: <?= number_format($sisaBobot, 2) ?></span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filter & Tombol Aksi -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <!-- Limit -->
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show</label>
                <form method="GET" action="<?= e(url('kriteria')); ?>" id="formLimit">
                    <input type="hidden" name="url" value="kriteria">
                    <select name="limit" class="form-select form-select-sm d-inline-block w-auto" onchange="document.getElementById('formLimit').submit()">
                        <?php foreach ([10, 25, 50, 100] as $opt): ?>
                            <option value="<?= $opt; ?>" <?= $limit === $opt ? 'selected' : ''; ?>><?= $opt; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <label class="text-muted small mb-0">entries</label>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 text-center">
                <!-- Header Tabel -->
                <thead class="table-light">
                    <tr>
                        <th width="80">No</th>
                        <th width="200">Kode</th>
                        <th>Nama Kriteria</th>
                        <th width="200">Atribut</th>
                        <th width="200">Bobot</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <!-- Body Tabel -->
                <tbody>
                    <?php if (empty($kriteria)): ?>
                        <tr><td colspan="6" class="text-muted py-4">Belum ada data kriteria.</td></tr>
                    <?php else: ?>
                        <?php foreach ($kriteria as $i => $row): ?>
                            <!-- Data Baris -->
                            <tr>
                                <!-- No -->
                                <td><?= $offset + $i + 1 ?></td>
                                <!-- Kode -->
                                <td><?= e($row['kode_kriteria'] ?? '') ?></td>
                                <!-- Nama Kriteria -->
                                <td class="text-start fw-medium"><?= e($row['nama_kriteria']) ?></td>
                                <!-- Atribut -->
                                <td>
                                    <?php if (strtolower($row['atribut']) === 'benefit'): ?>
                                        <span class="badge" style="background:#dbeafe;color:#1d4ed8;">Benefit</span>
                                    <?php else: ?>
                                        <span class="badge" style="background:#fce7f3;color:#be185d;">Cost</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Bobot -->
                                <td><?= number_format($row['bobot'], 2) ?></td>
                                <!-- Aksi -->
                                <td>
                                    <!-- Edit -->
                                    <button class="btn btn-sm p-0 border-0 btn-kriteria-edit"
                                            title="Edit"
                                            data-id="<?= e($row['id_kriteria']) ?>"
                                            data-kode="<?= e($row['kode_kriteria'] ?? '') ?>"
                                            data-nama="<?= e($row['nama_kriteria']) ?>"
                                            data-atribut="<?= e($row['atribut']) ?>"
                                            data-bobot="<?= e($row['bobot']) ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditKriteria">
                                        <i class="bi bi-pencil-square fs-5" style="color:#f59e0b;"></i>
                                    </button>
                                    <span class="aksi-separator"></span>
                                    <!-- Hapus -->
                                    <button class="btn btn-sm p-0 border-0 btn-kriteria-hapus"
                                            title="Hapus"
                                            data-id="<?= e($row['id_kriteria']) ?>"
                                            data-nama="<?= e($row['nama_kriteria']) ?>"
                                            data-delete-url="<?= e(url('kriteria/delete')) ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalHapusKriteria">
                                        <i class="bi bi-trash3 fs-5" style="color:#ef4444;"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <!-- Footer Tabel -->
                <?php if (!empty($kriteria)): ?>
                <tfoot style="font-size: 1.05rem;">
                    <tr>
                        <!-- Total Bobot -->
                        <td colspan="6" class="text-center py-2 fw-bold" 
                            style="background-color: <?= $isBobotFull ? '#d1fae5' : '#fffbeb' ?>; 
                                   color: #212529; 
                                   border-top: 2px solid <?= $isBobotFull ? '#10b981' : '#f59e0b' ?>;
                                   border-bottom: 2px solid <?= $isBobotFull ? '#10b981' : '#f59e0b' ?>;">
                            TOTAL BOBOT : <?= number_format($totalBobot, 2) ?>
                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>

        <!-- Pagination -->
        <div class="row align-items-center mt-4">
            <div class="col-md-4">
                <p class="text-muted small mb-0">
                    <?php if ($total === 0): ?>
                        Tidak ada data.
                    <?php else: ?>
                        Show <?= $from; ?> to <?= $to; ?> of <?= $total; ?> entries
                    <?php endif; ?>
                </p>
            </div>
            <!-- Navigasi -->
            <div class="col-md-4 d-flex justify-content-center">
                <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= e($baseUrl . '&page=' . ($page - 1)); ?>">&#8249;</a>
                            </li>
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <?php if ($p === 1 || $p === $totalPages || abs($p - $page) <= 1): ?>
                                    <li class="page-item <?= $p === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?= e($baseUrl . '&page=' . $p); ?>"><?= $p; ?></a>
                                    </li>
                                <?php elseif (abs($p - $page) === 2): ?>
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= e($baseUrl . '&page=' . ($page + 1)); ?>">&#8250;</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</div>

<!-- Modal -->
<?php include __DIR__ . '/modal-tambah.php'; ?>
<?php include __DIR__ . '/modal-edit.php'; ?>
<?php include __DIR__ . '/modal-hapus.php'; ?>


<script>
    /* Sisa bobot tersedia dioper ke JS agar bisa dipakai di kriteria.js eksternal */
    const SISA_BOBOT = <?= number_format($sisaBobot, 2, '.', '') ?>;
</script>
<script src="<?= e(asset('assets/js/pages/kriteria.js')); ?>"></script>

