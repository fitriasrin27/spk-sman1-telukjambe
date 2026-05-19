<?php
/**
 * @var array $konversi
 * @var array $allKriteria
 * @var int   $filterKriteria
 * @var int   $total
 * @var int   $page
 * @var int   $limit
 * @var int   $totalPages
 * @var int   $offset
 * @var string|null $error
 */

$baseUrl = url('konversi');
$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $limit, $total);

// Pagination URL helper
$paginationUrl = $baseUrl . '&limit=' . $limit;
if ($filterKriteria > 0) $paginationUrl .= '&filter_kriteria=' . $filterKriteria;
?>
<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Konversi Nilai</h2>
</div>
<!-- Notifikasi -->
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <?= e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Filter Section -->
<div class="mb-4">
    <form method="GET" action="<?= e(url('konversi')); ?>">
        <input type="hidden" name="url" value="konversi">
        <input type="hidden" name="limit" value="<?= $limit ?>">
        <div class="row g-2 align-items-end">
            <!-- Filter Kriteria -->
            <div class="col-auto">
                <select name="filter_kriteria" class="form-select form-select-sm" style="min-width: 200px;">
                    <option value="0">Semua Kriteria</option>
                    <?php foreach ($allKriteria as $kr): ?>
                        <option value="<?= $kr['id_kriteria'] ?>" <?= $filterKriteria == $kr['id_kriteria'] ? 'selected' : '' ?>>
                            [<?= e($kr['kode_kriteria']) ?>] <?= e($kr['nama_kriteria']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Tombol Tampilkan -->
            <div class="col-auto">
                <button type="submit" class="btn btn-warning btn-sm text-white px-3">
                    <i class="bi bi-search me-1"></i>Tampilkan
                </button>
            </div>
            <!-- Reset Filter -->
            <?php if ($filterKriteria > 0): ?>
                <div class="col-auto">
                    <a href="<?= e(url('konversi')); ?>" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bi bi-x-lg me-1"></i>Reset
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tombol Tambah Konversi -->
<div class="mb-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKonversi">
        <i class="bi bi-plus-lg me-1"></i>Tambah Konversi
    </button>
</div>

<!-- Tabel Konversi -->
<div class="card">
    <div class="card-body">
        <!-- Jumlah Data -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show</label>
                <!-- Form Limit -->
                <form method="GET" action="<?= e(url('konversi')); ?>" id="formLimit">
                    <input type="hidden" name="url" value="konversi">
                    <!-- Filter Kriteria -->
                    <?php if ($filterKriteria > 0): ?>
                        <input type="hidden" name="filter_kriteria" value="<?= $filterKriteria ?>">
                    <?php endif; ?>
                    <!-- Limit -->
                    <select name="limit" class="form-select form-select-sm d-inline-block w-auto" onchange="document.getElementById('formLimit').submit()">
                        <?php foreach ([10, 25, 50, 100] as $opt): ?>
                            <option value="<?= $opt; ?>" <?= $limit === $opt ? 'selected' : ''; ?>><?= $opt; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <label class="text-muted small mb-0">entries</label>
            </div>
        </div>
        <!-- Tabel Konversi -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 text-center">
                <thead class="table-light">
                    <!-- Judul Tabel -->
                    <tr>
                        <th width="70">No</th>
                        <th width="130">Kode</th>
                        <th>Nama Kriteria</th>
                        <th width="200">Nilai Asli (Label)</th>
                        <th width="200">Nilai Konversi (Skor)</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data Konversi (Jika tidak ada) -->
                    <?php if (empty($konversi)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Data konversi belum tersedia.</td>
                        </tr>
                    <?php else: ?>
                        <!-- Data Konversi -->
                        <?php foreach ($konversi as $i => $row): ?>
                            <tr>
                                <!-- Nomor -->
                                <td><?= $offset + $i + 1 ?></td>
                                <!-- Kode Kriteria -->
                                <td><?= e($row['kode_kriteria']) ?></td>
                                <!-- Nama Kriteria -->
                                <td class="text-start fw-medium"><?= e($row['nama_kriteria']) ?></td>
                                <!-- Nilai Asli (Label) -->
                                <td><?= e($row['nilai_asli']) ?></td>
                                <!-- Nilai Konversi (Skor) -->
                                <td><?= number_format($row['nilai_konversi'], 2) ?></td>
                                <!-- Tombol Aksi -->
                                <td>
                                    <!-- Tombol Edit -->
                                    <button class="btn btn-sm p-0 border-0 btn-konversi-edit"
                                            title="Edit"
                                            data-id="<?= e($row['id_konversi']) ?>"
                                            data-kriteria="<?= e($row['id_kriteria']) ?>"
                                            data-asli="<?= e($row['nilai_asli']) ?>"
                                            data-konversi="<?= e($row['nilai_konversi']) ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditKonversi">
                                        <i class="bi bi-pencil-square fs-5" style="color:#f59e0b;"></i>
                                    </button>
                                    <span class="aksi-separator"></span>
                                    <!-- Tombol Hapus -->
                                    <button class="btn btn-sm p-0 border-0 btn-konversi-hapus"
                                            title="Hapus"
                                            data-id="<?= e($row['id_konversi']) ?>"
                                            data-nama="<?= e($row['nilai_asli'] . ' (' . $row['nama_kriteria'] . ')') ?>"
                                            data-delete-url="<?= e(url('konversi/delete')) ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalHapusKonversi">
                                        <i class="bi bi-trash3 fs-5" style="color:#ef4444;"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="row align-items-center mt-4">
            <!-- Info Jumlah Data -->
            <div class="col-md-6">
                <p class="text-muted small mb-0">
                    Show <?= $from; ?> to <?= $to; ?> of <?= $total; ?> entries
                </p>
            </div>
            <!-- Pagination -->
            <div class="col-md-6 d-flex justify-content-center">
                <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= $paginationUrl . '&page=' . ($page - 1); ?>">Previous</a>
                            </li>
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <li class="page-item <?= $page === $p ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?= $paginationUrl . '&page=' . $p; ?>"><?= $p; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= $paginationUrl . '&page=' . ($page + 1); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<?php include __DIR__ . '/modal-tambah.php'; ?>
<?php include __DIR__ . '/modal-edit.php'; ?>
<?php include __DIR__ . '/modal-hapus.php'; ?>


<script src="<?= e(asset('assets/js/pages/konversi.js')); ?>"></script>

