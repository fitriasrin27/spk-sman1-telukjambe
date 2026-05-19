<?php
/**
 * Variabel yang di-inject oleh MataPelajaranController
 *
 * @var string $tingkat
 * @var string $jurusan
 * @var int $limit
 * @var int $page
 * @var int $total
 * @var int $totalPages
 * @var int $offset
 * @var array $mapelList
 * @var array $daftarJurusan
 * @var string|null $error
 */

use App\Models\MataPelajaran;

// Filter URL
$filterQuery = http_build_query(array_filter([
    'tingkat' => $tingkat,
    'jurusan' => $jurusan,
    'q'       => $search,
    'limit'   => $limit !== 10 ? $limit : '',
]));
$baseUrl = url('mata-pelajaran') . ($filterQuery ? '&' . $filterQuery : '');

$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $limit, $total);
?>
<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Mata Pelajaran</h2>
</div>
<!-- Notifikasi Error -->
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Filter Tingkat dan Jurusan -->
<div class="mb-4">
    <form method="GET" action="<?= e(url('')); ?>">
        <input type="hidden" name="url" value="mata-pelajaran">
        <div class="row g-2 align-items-end">
            <!-- Filter Tingkat -->
            <div class="col-auto">
                <select name="tingkat" class="form-select form-select-sm" style="min-width: 120px;">
                    <option value="">Semua Tingkat</option>
                    <option value="X" <?= $tingkat === 'X' ? 'selected' : ''; ?>>X</option>
                    <option value="XI" <?= $tingkat === 'XI' ? 'selected' : ''; ?>>XI</option>
                    <option value="XII" <?= $tingkat === 'XII' ? 'selected' : ''; ?>>XII</option>
                </select>
            </div>
            <!-- Filter Jurusan -->
            <div class="col-auto">
                <select name="jurusan" class="form-select form-select-sm" style="min-width: 150px;">
                    <option value="">Semua Jurusan</option>
                    <?php foreach ($daftarJurusan as $j): ?>
                        <option value="<?= e($j); ?>" <?= $jurusan === $j ? 'selected' : ''; ?>>
                            <?= e($j); ?>
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
            <!-- Tombol Reset -->
            <?php if ($tingkat !== '' || $jurusan !== ''): ?>
                <div class="col-auto">
                    <a href="<?= e(url('mata-pelajaran')); ?>" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bi bi-x-lg me-1"></i>Reset
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>
<!-- Tombol Tambah Mapel -->
<?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
<div class="d-flex gap-2 mb-4 btn-group-main">
    <button type="button" class="btn btn-sm btn-primary px-3" data-bs-toggle="modal" data-bs-target="#modalTambahMapel">
        <i class="bi bi-plus-lg me-1"></i>Tambah Mapel
    </button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <!-- Header Tabel -->
        <!-- Row 1: Tombol Hapus Data di Atas -->
        <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-outline-danger btn-sm fw-medium px-3 shadow-sm" id="btnModeHapus" <?= empty($mapelList) ? 'disabled' : '' ?>>
                <i class="bi bi-trash me-1"></i>Hapus Data
            </button>
        </div>
        <?php endif; ?>

        <!-- Row 2: Show entries & Search Bar -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show</label>
                <form method="GET" action="<?= e(url('')); ?>" id="formLimit">
                    <input type="hidden" name="url"     value="mata-pelajaran">
                    <input type="hidden" name="tingkat" value="<?= e($tingkat); ?>">
                    <input type="hidden" name="jurusan" value="<?= e($jurusan); ?>">
                    <input type="hidden" name="q"       value="<?= e($search); ?>">
                    <select name="limit" class="form-select form-select-sm d-inline-block w-auto" 
                            onchange="document.getElementById('formLimit').submit()">
                        <option value="10" <?= $limit === 10 ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?= $limit === 25 ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?= $limit === 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?= $limit === 100 ? 'selected' : ''; ?>>100</option>
                    </select>
                </form>
                <label class="text-muted small mb-0">entries</label>
            </div>
            <!-- Search Bar -->
            <div class="ms-auto" style="min-width: 250px;">
                <form method="GET" action="<?= e(url('')); ?>" id="formSearch">
                    <input type="hidden" name="url"     value="mata-pelajaran">
                    <input type="hidden" name="tingkat" value="<?= e($tingkat); ?>">
                    <input type="hidden" name="jurusan" value="<?= e($jurusan); ?>">
                    <input type="hidden" name="limit"   value="<?= e($limit); ?>">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="q" id="searchQueryInput" class="form-control border-start-0 ps-0" 
                               placeholder="Cari kode, nama, tingkat atau jurusan..." autocomplete="off"
                               value="<?= e($search); ?>">
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <!-- Checkbox -->
                        <th width="40" class="text-center col-checkbox d-none">
                            <div class="d-flex justify-content-center">
                                <input type="checkbox" class="form-check-input m-0" id="selectAll">
                            </div>
                        </th>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th class="text-center" style="width: 140px;">Kode Mapel</th>
                        <th class="text-center" style="padding-left: 20px;">Nama Mapel</th>
                        <th class="text-center" style="width: 130px;">Tingkat</th>
                        <th class="text-center" style="width: 130px;">Jurusan</th>
                        <!-- Aksi -->
                        <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
                        <th class="text-center col-aksi" style="width: 150px;">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data Mapel -->
                    <?php if (empty($mapelList)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada data mata pelajaran.
                            </td>
                        </tr>
                    <?php else: ?>
                        <!-- Looping Data Mapel -->
                        <?php $i = $offset + 1; foreach ($mapelList as $m): ?>
                            <tr>
                                <!-- Checkbox -->
                                <td class="text-center col-checkbox d-none">
                                    <div class="d-flex justify-content-center">
                                        <input type="checkbox" class="form-check-input item-checkbox m-0" value="<?= e($m['id_mapel']); ?>">
                                    </div>
                                </td>
                                <!-- No -->
                                <td class="text-center text-muted"><?= $i++; ?></td>
                                <!-- Kode Mapel -->
                                <td class="fw-medium"><?= e($m['kode_mapel']); ?></td>
                                <!-- Nama Mapel -->
                                <td><?= e($m['nama_mapel']); ?></td>
                                <!-- Tingkat -->
                                <td class="text-center">
                                    <?= MataPelajaran::getBadgeTingkat($m['tingkat']); ?>
                                </td>
                                <!-- Jurusan -->
                                <td class="text-center">
                                    <?= MataPelajaran::getBadgeJurusan($m['jurusan']); ?>
                                </td>
                                <!-- Aksi -->
                                <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
                                <td class="text-center col-aksi">
                                    <!-- Tombol Edit -->
                                    <button class="btn btn-sm p-0 border-0 btn-edit-mapel" 
                                            title="Edit"
                                            data-id="<?= e($m['id_mapel']); ?>"
                                            data-kode="<?= e($m['kode_mapel']); ?>"
                                            data-nama="<?= e($m['nama_mapel']); ?>"
                                            data-tingkat="<?= e($m['tingkat']); ?>"
                                            data-jurusan="<?= e($m['jurusan']); ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditMapel">
                                        <i class="bi bi-pencil-square fs-5" style="color:#f59e0b;"></i>
                                    </button>
                                    <span class="aksi-separator"></span>
                                    <!-- Tombol Hapus -->
                                    <button class="btn btn-sm p-0 border-0 btn-hapus-mapel"
                                            title="Hapus"
                                            data-id="<?= e($m['id_mapel']); ?>"
                                            data-kode="<?= e($m['kode_mapel']); ?>"
                                            data-nama="<?= e($m['nama_mapel']); ?>"
                                            data-delete-url="<?= e(url('mata-pelajaran/delete')); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalHapusMapel">
                                        <i class="bi bi-trash3 fs-5" style="color:#ef4444;"></i>
                                    </button>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
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
            <!-- Pagination -->
            <div class="col-md-4 d-flex justify-content-center">
                <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous Button -->
                            <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= e($baseUrl . '&page=' . ($page - 1)); ?>">&#8249;</a>
                            </li>
                            <!-- Page Numbers -->
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <?php if ($p === 1 || $p === $totalPages || abs($p - $page) <= 1): ?>
                                    <li class="page-item <?= $p === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?= e($baseUrl . '&page=' . $p); ?>"><?= $p; ?></a>
                                    </li>
                                <?php elseif (abs($p - $page) === 2): ?>
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <!-- Next Button -->
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

<!-- Toolbar Aksi Hapus (Floating/Fixed at bottom of card) -->
<div id="batchToolbar" class="bg-white border-top p-3 d-none shadow-lg" 
     style="position: sticky; bottom: 0; z-index: 1020; margin: 0 -1.25rem -1.25rem -1.25rem; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <div class="fw-bold text-danger">
                <i class="bi bi-check2-all me-1"></i>
                <span id="selectedCount">0</span> Data Terpilih
            </div>
            <div class="text-muted small d-none d-md-block">Centang mata pelajaran yang ingin dihapus sekaligus.</div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-light px-4" id="cancelBatch">Batal</button>
            <button type="button" class="btn btn-danger px-4 fw-medium" id="btnHapusBatch">
                <i class="bi bi-trash me-1"></i>Hapus Terpilih
            </button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/modal-tambah.php'; ?>
<?php include __DIR__ . '/modal-edit.php'; ?>
<?php include __DIR__ . '/modal-hapus.php'; ?>


<script src="<?= e(asset('assets/js/pages/mata-pelajaran.js')); ?>"></script>

