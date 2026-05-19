<?php
/**
 * @var string $tahunAjaran
 * @var string $kelas
 * @var int    $semester
 * @var int    $limit
 * @var int    $page
 * @var int    $total
 * @var int    $totalPages
 * @var int    $offset
 * @var array  $riwayat
 * @var array  $daftarTahunAjaran
 * @var array  $daftarKelas
 * @var string|null $error
 */

use App\Models\RiwayatKelas;

$filterQuery = http_build_query(array_filter([
    'tahun_ajaran' => $tahunAjaran,
    'kelas'        => $kelas,
    'semester'     => $semester ?: '',
    'q'            => $search,
    'limit'        => $limit !== 10 ? $limit : '',
]));
$baseUrl = url('riwayat-kelas') . ($filterQuery ? '&' . $filterQuery : '');

$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $limit, $total);
?>
<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Riwayat Kelas</h2>
</div>

<!-- Pesan Error -->
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Filter -->
<div class="mb-4">
    <form method="GET" action="<?= e(url('riwayat-kelas')); ?>">
        <input type="hidden" name="url" value="riwayat-kelas">
        <div class="row g-2 align-items-end">
            <!-- Filter Tahun Ajaran -->
            <div class="col-auto">
                <select id="filterTahunAjaran" name="tahun_ajaran" class="form-select form-select-sm" style="min-width:160px;">
                    <option value="">Tahun Ajaran</option>
                    <?php foreach ($daftarTahunAjaran as $ta): ?>
                        <option value="<?= e($ta); ?>" <?= $tahunAjaran === $ta ? 'selected' : ''; ?>>
                            <?= e($ta); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Filter Kelas -->
            <div class="col-auto">
                <select id="filterKelas" name="kelas" class="form-select form-select-sm" style="min-width:160px;">
                    <option value="">Kelas</option>
                    <?php foreach ($daftarKelas as $k): ?>
                        <option value="<?= e($k); ?>" <?= $kelas === $k ? 'selected' : ''; ?>>
                            <?= e($k); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Filter Semester -->
            <div class="col-auto">
                <select id="filterSemester" name="semester" class="form-select form-select-sm" style="min-width:150px;">
                    <option value="">Semester</option>
                    <?php foreach (range(1, 6) as $s): ?>
                        <option value="<?= $s; ?>" <?= $semester === $s ? 'selected' : ''; ?>>
                            <?= RiwayatKelas::labelSemester($s); ?>
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
            <?php if ($tahunAjaran || $kelas || $semester): ?>
                <div class="col-auto">
                    <a href="<?= e(url('riwayat-kelas')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i>Reset
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tombol Aksi -->
<?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
<div class="d-flex gap-2 mb-4 btn-group-main">
    <!-- Tombol Tambah -->
    <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahRiwayat">
        <i class="bi bi-plus-lg me-1"></i>Tambah Penempatan
    </button>
    <!-- Tombol Upload -->
    <button class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalUploadRiwayat">
        <i class="bi bi-file-earmark-excel me-1"></i>Upload Data Excel
    </button>
</div>
<?php endif; ?>

<!-- Tabel -->
<div class="card">
    <div class="card-body">
        <!-- Row 1: Tombol Hapus Data di Atas -->
        <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
        <div class="d-flex justify-content-end mb-3">
            <button type="button" class="btn btn-outline-danger btn-sm fw-medium px-3 shadow-sm" id="btnModeHapus" <?= empty($riwayat) ? 'disabled' : '' ?>>
                <i class="bi bi-trash me-1"></i>Hapus Data
            </button>
        </div>
        <?php endif; ?>

        <!-- Row 2: Show entries & Search Bar -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show</label>
                <form method="GET" action="<?= e(url('')); ?>" id="formLimit">
                    <input type="hidden" name="url"          value="riwayat-kelas">
                    <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran); ?>">
                    <input type="hidden" name="kelas"        value="<?= e($kelas); ?>">
                    <input type="hidden" name="semester"     value="<?= e($semester ?: ''); ?>">
                    <input type="hidden" name="q"            value="<?= e($search); ?>">
                    <select name="limit" class="form-select form-select-sm d-inline-block w-auto"
                            onchange="document.getElementById('formLimit').submit()">
                        <?php foreach ([10, 25, 50, 100] as $opt): ?>
                            <option value="<?= $opt; ?>" <?= $limit === $opt ? 'selected' : ''; ?>><?= $opt; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <label class="text-muted small mb-0">entries</label>
            </div>
            
            <!-- Search Input -->
            <div class="ms-auto" style="min-width: 250px;">
                <form method="GET" action="<?= e(url('')); ?>" id="formSearch">
                    <input type="hidden" name="url"          value="riwayat-kelas">
                    <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran); ?>">
                    <input type="hidden" name="kelas"        value="<?= e($kelas); ?>">
                    <input type="hidden" name="semester"     value="<?= e($semester ?: ''); ?>">
                    <input type="hidden" name="limit"        value="<?= e($limit); ?>">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="q" id="searchQueryInput" class="form-control border-start-0 ps-0" 
                               placeholder="Cari nama, NISN, NIS, kelas..." autocomplete="off"
                               value="<?= e($search); ?>">
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 table-riwayat">
                <!-- Header Tabel -->
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center col-checkbox d-none">
                            <div class="d-flex justify-content-center">
                                <input type="checkbox" class="form-check-input m-0" id="selectAll">
                            </div>
                        </th>
                        <th width="50"  class="text-center">No</th>
                        <th             class="text-center">Nama Siswa</th>
                        <th width="120" class="text-center">NISN</th>
                        <th width="120" class="text-center">NIS</th>
                        <th width="70"  class="text-center">L/P</th>
                        <th width="130" class="text-center">Tahun Ajaran</th>
                        <th width="160" class="text-center">Kelas</th>
                        <th width="150" class="text-center">Semester</th>
                        <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
                        <th width="120" class="text-center col-aksi">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <!-- Body Tabel -->
                <tbody>
                    <?php if (empty($riwayat)): ?>
                        <!-- Notifikasi Data Kosong -->
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="bi bi-journal-x fs-3 d-block mb-2 opacity-50"></i>
                                Belum ada data riwayat kelas.
                            </td>
                        </tr>
                    <?php else: ?>
                        <!-- List Data -->
                        <?php foreach ($riwayat as $i => $r): ?>
                            <?php
                                $noRiwayat = $r['id_riwayat'] === null;
                                $semNum    = (int) ($r['semester'] ?? 0);
                                $badgeClass = $semNum > 0 ? RiwayatKelas::badgeClass($semNum) : '';
                                $semLabel  = $semNum > 0 ? RiwayatKelas::labelSemester($semNum) : null;
                                // Deteksi jenis semester (ganjil/genap) untuk pre-fill edit
                                $semJenis  = $semNum % 2 === 0 ? 'genap' : 'ganjil';
                            ?>

                            <!-- Row Tabel -->
                            <tr class="<?= $noRiwayat ? 'rk-no-placement' : ''; ?>">
                                
                                <!-- Checkbox -->
                                <td class="text-center col-checkbox d-none">
                                    <?php if (!$noRiwayat): ?>
                                        <div class="d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input item-checkbox m-0" value="<?= e($r['id_riwayat']); ?>">
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <!-- No -->
                                <td class="text-center"><?= $offset + $i + 1; ?></td>
                                <!-- Nama Siswa -->
                                <td>
                                    <?= e($r['nama']); ?>
                                    <?php if ($noRiwayat): ?>
                                        <span class="badge ms-1" style="background:#fff3cd;color:#856404;font-size:0.68rem;">Belum ada penempatan</span>
                                    <?php endif; ?>
                                </td>
                                <!-- NISN -->
                                <td class="text-center"><?= e($r['nisn']); ?></td>
                                <!-- NIS -->
                                <td class="text-center"><?= e($r['nis']); ?></td>
                                <!-- Jenis Kelamin -->
                                <td class="text-center">
                                    <?php if ($r['jenis_kelamin'] === 'L'): ?>
                                        <span class="badge rounded-pill" style="background:#dbeafe;color:#1d4ed8;">L</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill" style="background:#fce7f3;color:#be185d;">P</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Tahun Ajaran -->
                                <td class="text-center"><?= $r['tahun_ajaran'] ? e($r['tahun_ajaran']) : '—'; ?></td>
                                <!-- Kelas -->
                                <td class="text-center"><?= $r['kelas'] ? e($r['kelas']) : '—'; ?></td>
                                <!-- Semester -->
                                <td class="text-center">
                                    <?php if ($semLabel): ?>
                                        <span class="badge-semester <?= $badgeClass ?>"><?= $semLabel ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Aksi -->
                                <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'bk'])): ?>
                                <td class="text-center col-aksi">
                                    <?php if ($noRiwayat): ?>
                                        <!-- Siswa tanpa riwayat: tombol tambah saja -->
                                        <button class="btn btn-sm p-0 border-0 btn-rk-tambah"
                                                title="Tambah Penempatan"
                                                data-id-siswa="<?= e($r['id_siswa']); ?>"
                                                data-nama="<?= e($r['nama']); ?>"
                                                data-nisn="<?= e($r['nisn']); ?>"
                                                data-nis="<?= e($r['nis']); ?>"
                                                data-gender="<?= e($r['jenis_kelamin']); ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalTambahRiwayat">
                                            <i class="bi bi-plus-circle-fill fs-5" style="color:#0F6CBD;"></i>
                                        </button>
                                    <?php else: ?>
                                        <!-- edit -->
                                        <button class="btn btn-sm p-0 border-0 btn-rk-edit"
                                                title="Edit"
                                                data-id="<?= e($r['id_riwayat']); ?>"
                                                data-nama="<?= e($r['nama']); ?>"
                                                data-nisn="<?= e($r['nisn']); ?>"
                                                data-nis="<?= e($r['nis']); ?>"
                                                data-gender="<?= e($r['jenis_kelamin']); ?>"
                                                data-tahun="<?= e($r['tahun_ajaran']); ?>"
                                                data-kelas="<?= e($r['kelas']); ?>"
                                                data-semjenis="<?= $semJenis; ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditRiwayat">
                                            <i class="bi bi-pencil-square fs-5" style="color:#f59e0b;"></i>
                                        </button>
                                        <!-- hapus -->
                                        <span class="aksi-separator"></span>
                                        <button class="btn btn-sm p-0 border-0 btn-rk-hapus"
                                                title="Hapus"
                                                data-id="<?= e($r['id_riwayat']); ?>"
                                                data-nama="<?= e($r['nama']); ?>"
                                                data-sem-label="<?= $semLabel ? e($semLabel) : ''; ?>"
                                                data-delete-url="<?= e(url('riwayat-kelas/delete')); ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalHapusRiwayat">
                                            <i class="bi bi-trash3 fs-5" style="color:#ef4444;"></i>
                                        </button>
                                    <?php endif; ?>
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
            <!-- Informasi Jumlah Data -->
            <div class="col-md-4">
                <p class="text-muted small mb-0">
                    <?php if ($total === 0): ?>
                        Tidak ada data.
                    <?php else: ?>
                        Show <?= $from; ?> to <?= $to; ?> of <?= $total; ?> entries
                    <?php endif; ?>
                </p>
            </div>
            <!-- Navigasi Halaman -->
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

<?php include __DIR__ . '/modal-tambah.php'; ?>
<?php include __DIR__ . '/modal-edit.php'; ?>
<?php include __DIR__ . '/modal-hapus.php'; ?>
<?php include __DIR__ . '/modal-upload.php'; ?>
<?php include __DIR__ . '/modal-template.php'; ?>

<script src="<?= e(asset('assets/js/pages/riwayat-kelas.js')); ?>"></script>

