<?php
/**
 * Variabel yang di-inject oleh SiswaController melalui extract($data)
 *
 * @var string      $tahunAjaran
 * @var string      $kelas
 * @var int         $limit
 * @var int         $page
 * @var int         $total
 * @var int         $totalPages
 * @var int         $offset
 * @var array       $siswa
 * @var array       $daftarTahunAjaran
 * @var array       $daftarKelas
 * @var string|null $error
 */

// Bangun query string untuk filter (pertahankan filter aktif saat ganti halaman)
$filterQuery = http_build_query(array_filter([
    'tahun_ajaran' => $tahunAjaran,
    'kelas'        => $kelas,
    'q'            => $search,
    'limit'        => $limit !== 10 ? $limit : '',
]));
$baseUrl = url('siswa') . ($filterQuery ? '&' . $filterQuery : '');
// Hitung halaman
$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $limit, $total);
?>
<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Identitas Siswa</h2>
</div>

<!-- Pesan Error -->
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>
<!-- Filter Siswa -->
<div class="mb-4">
    <form method="GET" action="<?= e(url('siswa')); ?>">
        <input type="hidden" name="url" value="siswa">
        <div class="row g-2 align-items-end">
            <!-- Tahun Ajaran -->
            <div class="col-auto">
                <select id="filterTahunAjaran" name="tahun_ajaran" class="form-select form-select-sm" style="min-width: 160px;">
                    <option value="">Tahun Ajaran</option>
                    <?php foreach ($daftarTahunAjaran as $ta): ?>
                        <option value="<?= e($ta); ?>" <?= $tahunAjaran === $ta ? 'selected' : ''; ?>>
                            <?= e($ta); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Kelas -->
            <div class="col-auto">
                <select id="filterKelas" name="kelas" class="form-select form-select-sm" style="min-width: 160px;">
                    <option value="">Kelas</option>
                    <?php foreach ($daftarKelas as $k): ?>
                        <option value="<?= e($k); ?>" <?= $kelas === $k ? 'selected' : ''; ?>>
                            <?= e($k); ?>
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
            <?php if ($tahunAjaran || $kelas): ?>
                <div class="col-auto">
                    <a href="<?= e(url('siswa')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i>Reset
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <!-- Tombol Aksi -->
    <div class="d-flex gap-2">
        <!-- Tombol Tambah -->
        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-lg me-1"></i>Tambah Siswa
        </button>
        <!-- Tombol Upload -->
        <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalUpload">
            <i class="bi bi-file-earmark-excel me-1"></i>Upload Data Excel
        </button>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <!-- Row 1: Tombol Hapus Data di Atas -->
        <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-outline-danger btn-sm fw-medium px-3 shadow-sm" id="btnModeHapus" <?= empty($siswa) ? 'disabled' : '' ?>>
                <i class="bi bi-trash me-1"></i>Hapus Data
            </button>
        </div>
        <?php endif; ?>

        <!-- Row 2: Show entries & Search Bar -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <!-- Show entries -->
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show</label>
                <form method="GET" action="<?= e(url('')); ?>" id="formLimit">
                    <input type="hidden" name="url"          value="siswa">
                    <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran); ?>">
                    <input type="hidden" name="kelas"        value="<?= e($kelas); ?>">
                    <input type="hidden" name="q"            value="<?= e($search); ?>">
                    <select name="limit" class="form-select form-select-sm d-inline-block w-auto"
                            onchange="document.getElementById('formLimit').submit()">
                        <?php foreach ([10, 25, 50, 100] as $opt): ?>
                            <option value="<?= $opt; ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <label class="text-muted small mb-0">entries</label>
            </div>
            
            <!-- Search Bar -->
            <div class="ms-auto" style="min-width: 250px;">
                <form method="GET" action="<?= e(url('')); ?>" id="formSearch">
                    <input type="hidden" name="url"          value="siswa">
                    <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran); ?>">
                    <input type="hidden" name="kelas"        value="<?= e($kelas); ?>">
                    <input type="hidden" name="limit"        value="<?= e($limit); ?>">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="q" id="searchQueryInput" class="form-control border-start-0 ps-0" 
                               placeholder="Cari nama, NISN atau NIS..." autocomplete="off"
                               value="<?= e($search); ?>">
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <!-- Tabel Siswa -->
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <!-- Checkbox (Hanya muncul untuk user tertentu) -->
                        <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
                        <th width="40" class="text-center col-checkbox d-none">
                            <div class="d-flex justify-content-center">
                                <input class="form-check-input m-0" type="checkbox" id="selectAllSiswa">
                            </div>
                        </th>
                        <?php endif; ?>
                        <!-- Header Tabel -->
                        <th width="50" class="text-center">No</th>
                        <th class="text-center">Nama Lengkap</th>
                        <th width="250" class="text-center">NISN</th>
                        <th width="250" class="text-center">NIS</th>
                        <th width="70" class="text-center">L/P</th>
                        <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
                        <th width="150" class="text-center col-aksi">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($siswa)): ?>
                        <!-- Pesan Jika Tidak Ada Data -->
                        <tr>
                            <td colspan="<?= !in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk']) ? 7 : 6 ?>" class="text-center text-muted py-5">
                                <i class="bi bi-person-x fs-3 d-block mb-2 opacity-50"></i>
                                Belum ada data siswa.
                            </td>
                        </tr>
                    <?php else: ?>
                        <!-- Data Siswa -->
                        <?php foreach ($siswa as $i => $s): ?>
                            <tr class="siswa-row" data-id="<?= $s['id_siswa'] ?>">
                                <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
                                <td class="text-center col-checkbox d-none">
                                    <div class="d-flex justify-content-center">
                                        <input class="form-check-input chk-siswa m-0" type="checkbox" value="<?= $s['id_siswa'] ?>">
                                    </div>
                                </td>
                                <?php endif; ?>
                                <!-- No -->
                                <td class="text-center"><?= $offset + $i + 1; ?></td>
                                <!-- Nama Lengkap -->
                                <td><?= e($s['nama']); ?></td>
                                <!-- NISN -->
                                <td class="text-center"><?= e($s['nisn']); ?></td>
                                <!-- NIS -->
                                <td class="text-center"><?= e($s['nis']); ?></td>
                                <!-- Jenis Kelamin -->
                                <td class="text-center">
                                    <?php if ($s['jenis_kelamin'] === 'L'): ?>
                                        <!-- Laki-laki -->
                                        <span class="badge rounded-pill" style="background:#dbeafe;color:#1d4ed8;">L</span>
                                    <?php else: ?>
                                        <!-- Perempuan -->
                                        <span class="badge rounded-pill" style="background:#fce7f3;color:#be185d;">P</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Aksi -->
                                <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
                                <td class="text-center col-aksi">
                                    <!-- Edit -->
                                    <button class="btn btn-sm p-0 border-0 btn-edit"
                                            title="Edit"
                                            data-id="<?= e($s['id_siswa']); ?>"
                                            data-nama="<?= e($s['nama']); ?>"
                                            data-nisn="<?= e($s['nisn']); ?>"
                                            data-nis="<?= e($s['nis']); ?>"
                                            data-gender="<?= e($s['jenis_kelamin']); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEdit">
                                        <i class="bi bi-pencil-square fs-5" style="color:#f59e0b;"></i>
                                    </button>
                                    <span class="aksi-separator"></span>
                                    <!-- Hapus -->
                                    <button class="btn btn-sm p-0 border-0 btn-hapus"
                                            title="Hapus"
                                            data-id="<?= e($s['id_siswa']); ?>"
                                            data-nama="<?= e($s['nama']); ?>"
                                            data-nisn="<?= e($s['nisn']); ?>"
                                            data-delete-url="<?= e(url('siswa/delete')); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalHapus">
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

        <div class="row align-items-center mt-4">
            <div class="col-md-4">
                <!-- Show entries -->
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
                            <!-- Previous Page -->
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
                            <li class="page-item <?= $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= e($baseUrl . '&page=' . ($page + 1)); ?>">&#8250;</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Toolbar Aksi Hapus (Floating/Fixed at bottom of card) -->
<div id="toolbarHapusSiswa" class="bg-white border-top p-3 d-none shadow-lg" 
     style="position: sticky; bottom: 0; z-index: 1020; margin: 0 -1.25rem -1.25rem -1.25rem; border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem;">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <!-- Jumlah Terpilih -->
            <div class="fw-bold text-danger">
                <i class="bi bi-check2-all me-1"></i>
                <span id="txtJumlahTerpilih">0</span> Data Terpilih
            </div>
            <!-- Deskripsi -->
            <div class="text-muted small d-none d-md-block">Centang data siswa yang ingin dihapus sekaligus.</div>
        </div>
        <div class="d-flex gap-2">
            <!-- Batal -->
            <button type="button" class="btn btn-light px-4" id="btnBatalHapus">Batal</button>
            <!-- Hapus Terpilih -->
            <button type="button" class="btn btn-danger px-4 fw-medium" id="btnKonfirmasiHapusBatch" disabled>
                <i class="bi bi-trash me-1"></i>Hapus Terpilih
            </button>
        </div>
    </div>
</div>

<!-- Popup Modals -->
<?php include __DIR__ . '/modal-tambah.php'; ?>
<?php include __DIR__ . '/modal-edit.php'; ?>
<?php include __DIR__ . '/modal-hapus.php'; ?>
<?php include __DIR__ . '/modal-upload.php'; ?>

<!-- Script -->
<script src="<?= e(asset('assets/js/pages/siswa.js')); ?>"></script>