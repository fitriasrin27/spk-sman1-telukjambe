<?php
date_default_timezone_set('Asia/Jakarta');
/**
 * Variabel yang di-inject oleh NilaiController
 *
 * @var string $tahunAjaran
 * @var string $kelas
 * @var string $semester
 * @var int $limit
 * @var int $page
 * @var int $total
 * @var int $totalPages
 * @var int $offset
 * @var array $nilaiList
 * @var array $optTahunAjaran
 * @var array $optKelas
 * @var array $optSemester
 * @var string|null $error
 */

use App\Models\RiwayatKelas;

// Filter URL
$status      = $status ?? '';
$tingkat     = $tingkat ?? '';
// filter tahun ajaran kelas semester status
$filterQuery = http_build_query(array_filter([
    'tahun_ajaran' => $tahunAjaran,
    'kelas'        => $kelas,
    'semester'     => $semester,
    'status'       => $status,
    'tingkat'      => $tingkat,
    'q'            => $search,
    'limit'        => $limit !== 10 ? $limit : '',
]));
$baseUrl = url('nilai') . ($filterQuery ? '&' . $filterQuery : '');
// from total > 0 ? offset + 1 : 0
$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $limit, $total);
?>
<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title">Data Nilai</h2>
</div>
<!-- Notifikasi Error -->
<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <?= e($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Filter Tahun Ajaran, Kelas, Semester -->
<div class="mb-4">
    <form method="GET" action="<?= e(url('')); ?>">
        <input type="hidden" name="url" value="nilai">
        <?php if ($status): ?><input type="hidden" name="status" value="<?= e($status); ?>"><?php endif; ?>
        <?php if ($tingkat): ?><input type="hidden" name="tingkat" value="<?= e($tingkat); ?>"><?php endif; ?>
        <div class="row g-2 align-items-end">
            <!-- Tahun Ajaran -->
            <div class="col-auto">
                <select name="tahun_ajaran" class="form-select form-select-sm" style="min-width: 160px;">
                    <option value="">Tahun Ajaran</option>
                    <?php foreach ($optTahunAjaran as $ta): ?>
                        <option value="<?= e($ta); ?>" <?= $tahunAjaran === $ta ? 'selected' : ''; ?>>
                            <?= e($ta); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Kelas -->
            <div class="col-auto">
                <select name="kelas" class="form-select form-select-sm" style="min-width: 160px;">
                    <option value="">Kelas</option>
                    <?php foreach ($optKelas as $k): ?>
                        <option value="<?= e($k); ?>" <?= $kelas === $k ? 'selected' : ''; ?>>
                            <?= e($k); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Semester -->
            <div class="col-auto">
                <select name="semester" class="form-select form-select-sm" style="min-width: 150px;">
                    <option value="">Semester</option>
                    <?php foreach ($optSemester as $sem): ?>
                        <option value="<?= e($sem); ?>" <?= $semester == $sem ? 'selected' : ''; ?>>
                            <?= RiwayatKelas::labelSemester($sem); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Button Tampilkan -->
            <div class="col-auto">
                <button type="submit" class="btn btn-warning btn-sm text-white px-3 fw-medium">
                    <i class="bi bi-search me-1"></i>Tampilkan
                </button>
            </div>
            <!-- Button Reset -->
            <?php if ($tahunAjaran || $kelas || $semester || $status || $tingkat): ?>
                <div class="col-auto">
                    <a href="<?= e(url('nilai')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i>Reset
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Filter Status dan Tingkat (untuk dashboard) -->
<?php if ($status === 'incomplete' || $tingkat): ?>
    <div class="d-flex align-items-center gap-2 mb-3">
        <span class="text-muted small">Filter Aktif:</span>
        <!-- Status -->
        <?php if ($status === 'incomplete'): ?>
            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger fw-normal">Hanya Belum Lengkap</span>
        <?php endif; ?>
        <!-- Tingkat -->
        <?php if ($tingkat): ?>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary fw-normal">Angkatan <?= e($tingkat); ?></span>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Tombol Tambah Data & Upload Data Excel -->
<?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'tu'])): ?>
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="d-flex gap-2">
        <!-- Button Tambah Data -->
        <button type="button" class="btn btn-sm btn-primary px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#modalTambahNilai">
            <i class="bi bi-plus-lg me-1"></i>Tambah Data
        </button>
        <!-- Button Upload Data Excel -->
        <button type="button" class="btn btn-sm btn-success px-3 fw-medium" data-bs-toggle="modal" data-bs-target="#modalUploadNilai">
            <i class="bi bi-file-earmark-arrow-up me-1"></i>Upload Data Excel
        </button>
    </div>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <!-- Header Tabel -->
        <!-- Row 1: Tombol Aksi di Atas -->
        <div class="d-flex justify-content-end align-items-center gap-2 mb-3">
            <?php if ($tahunAjaran && $kelas && $semester && current_user()['role'] !== 'bk'): ?>
                <!-- Button Cetak Leger (PDF) jika sudah pilih filter tahun ajaran, kelas, semester -->
                <button type="button" class="btn btn-sm btn-outline-info px-3 fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCetakLeger">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Cetak Leger (PDF)
                </button>
            <?php endif; ?>
            
            <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'tu'])): ?>
                <!-- Button Hapus Data -->
                <button type="button" class="btn btn-outline-danger btn-sm fw-medium px-3 shadow-sm" id="btnModeHapus" <?= empty($nilaiList) ? 'disabled' : '' ?>>
                    <i class="bi bi-trash me-1"></i>Hapus Data
                </button>
            <?php endif; ?>
        </div>

        <!-- Row 2: Show entries & Search Bar -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <!-- Show entries -->
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show</label>
                <form method="GET" action="<?= e(url('')); ?>" id="formLimit">
                    <input type="hidden" name="url" value="nilai">
                    <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran); ?>">
                    <input type="hidden" name="kelas"        value="<?= e($kelas); ?>">
                    <input type="hidden" name="semester"     value="<?= e($semester); ?>">
                    <input type="hidden" name="status"       value="<?= e($status); ?>">
                    <input type="hidden" name="tingkat"      value="<?= e($tingkat); ?>">
                    <input type="hidden" name="q"            value="<?= e($search); ?>">
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
                    <input type="hidden" name="url"          value="nilai">
                    <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran); ?>">
                    <input type="hidden" name="kelas"        value="<?= e($kelas); ?>">
                    <input type="hidden" name="semester"     value="<?= e($semester); ?>">
                    <input type="hidden" name="status"       value="<?= e($status); ?>">
                    <input type="hidden" name="tingkat"      value="<?= e($tingkat); ?>">
                    <input type="hidden" name="limit"        value="<?= e($limit); ?>">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="q" id="searchQueryInput" class="form-control border-start-0 ps-0" 
                               placeholder="Cari nama, NISN, NIS atau kelas..." autocomplete="off"
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
                        <!-- Checkbox untuk mode hapus (hanya tampil saat mode hapus aktif) -->
                        <th width="40" class="text-center col-checkbox d-none">
                            <div class="d-flex justify-content-center">
                                <input class="form-check-input m-0" type="checkbox" id="selectAllNilai">
                            </div>
                        </th>
                        <!-- Header Tabel -->
                        <th class="text-center" style="width: 50px;">No</th>
                        <th class="text-center">Nama Lengkap</th>
                        <th class="text-center" style="width: 120px;">NISN</th>
                        <th class="text-center" style="width: 120px;">NIS</th>
                        <th class="text-center" style="width: 110px;">Total Nilai</th>
                        <th class="text-center" style="width: 110px;">Rata-Rata</th>
                        <th class="text-center" style="width: 140px;">Status</th>
                        <th class="text-center col-aksi" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data List -->
                    <?php if (empty($nilaiList)): ?>
                        <!-- Jika tidak ada data -->
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada data nilai.
                            </td>
                        </tr>
                    <?php else: ?>
                        <!-- Perulangan Data List -->
                        <?php $i = $offset + 1; foreach ($nilaiList as $m): ?>
                            <?php 
                                // Syarat Status Nilai lengkap: 16 Mapel + Absensi
                                $isLengkap = ($m['id_absensi'] && $m['jumlah_mapel_diisi'] >= 16);
                                
                                // Opsi 1: rata-rata selalu dibagi total mapel kelas (bukan yang terisi saja)
                                $rataRata = ($m['total_mapel_seharusnya'] > 0)
                                    ? (float)($m['total_nilai'] ?? 0) / (int)$m['total_mapel_seharusnya']
                                    : 0;
                            ?>
                            <!-- Baris Data List -->
                            <tr class="nilai-row" data-id="<?= $m['id_riwayat'] ?>">
                                <!-- Checkbox Mode Hapus -->
                                <td class="text-center col-checkbox d-none">
                                    <div class="d-flex justify-content-center">
                                        <input class="form-check-input chk-nilai m-0" type="checkbox" value="<?= $m['id_riwayat'] ?>">
                                    </div>
                                </td>
                                <!-- No -->
                                <td class="text-center text-muted"><?= $i++; ?></td>
                                <!-- Nama Lengkap -->
                                <td class="fw-medium"><?= e($m['nama']); ?></td>
                                <!-- NISN -->
                                <td class="text-center"><?= e($m['nisn']); ?></td>
                                <!-- NIS -->
                                <td class="text-center"><?= e($m['nis']); ?></td>
                                <!-- Total Nilai -->
                                <td class="text-center"><?= $m['total_nilai'] ? number_format((float)$m['total_nilai'], 2, ',', '.') : '-'; ?></td>
                                <!-- Rata-Rata -->
                                <td class="text-center"><?= $rataRata > 0 ? number_format($rataRata, 3, ',', '.') : '-'; ?></td>
                                <!-- Status -->
                                <td class="text-center col-aksi">
                                    <?php if ($isLengkap): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">Lengkap</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Belum Lengkap</span>
                                    <?php endif; ?>
                                </td>
                                <!-- TombolAksi -->
                                <td class="text-center col-aksi">
                                    <!-- Tombol Lihat Detail -->
                                    <button class="btn btn-sm p-0 border-0 text-success me-1" title="Lihat Detail"
                                            onclick="window.location.href='<?= e(url('nilai/detail&id=' . $m['id_riwayat'])); ?>'">
                                        <i class="bi bi-file-earmark-text fs-5"></i>
                                    </button>
                                    
                                    <!-- Tombol Edit -->
                                    <?php if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah', 'tu'])): ?>
                                        <span class="aksi-separator"></span>
                                        <button class="btn btn-sm p-0 border-0 text-warning mx-1" title="Edit"
                                                onclick="window.location.href='<?= e(url('nilai/edit&id=' . $m['id_riwayat'])); ?>'">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </button>
                                        <span class="aksi-separator"></span>
                                        <!-- Tombol Hapus -->
                                        <button class="btn btn-sm p-0 border-0 text-danger ms-1 btn-nilai-hapus" title="Hapus"
                                                data-bs-toggle="modal" data-bs-target="#modalHapusNilai"
                                                data-id="<?= $m['id_riwayat']; ?>"
                                                data-nama="<?= e($m['nama']); ?>"
                                                data-delete-url="<?= e(url('nilai/delete')); ?>">
                                            <i class="bi bi-trash3 fs-5"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="row align-items-center mt-4">
            <div class="col-md-4">
                <!-- Pagination Info -->
                <p class="text-muted small mb-0">
                    <!-- Jika tidak ada data -->
                    <?php if ($total === 0): ?>
                        <!-- Data tidak ditemukan -->
                        Tidak ada data.
                    <?php else: ?>
                        <!-- Jumlah Data -->
                        Show <?= $from; ?> to <?= $to; ?> of <?= $total; ?> entries
                    <?php endif; ?>
                </p>
            </div>

            <!-- Pagination Navigation -->
            <div class="col-md-4 d-flex justify-content-center">
                <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous Page -->
                            <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?= e($baseUrl . '&page=' . ($page - 1)); ?>">&#8249;</a>
                            </li>
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <?php if ($p === 1 || $p === $totalPages || abs($p - $page) <= 1): ?>
                                    <!-- Numbered Pages -->
                                    <li class="page-item <?= $p === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?= e($baseUrl . '&page=' . $p); ?>"><?= $p; ?></a>
                                    </li>
                                <?php elseif (abs($p - $page) === 2): ?>
                                    <!-- Ellipsis -->
                                    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <!-- Next Page -->
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

    </div>
</div>



<link rel="stylesheet" href="<?= e(asset('assets/css/pages/nilai.css')); ?>">
<script src="<?= e(asset('assets/js/pages/nilai.js')); ?>"></script>


<?php include __DIR__ . '/modal-tambah.php'; ?>
<?php include __DIR__ . '/modal-upload.php'; ?>
<?php include __DIR__ . '/modal-template.php'; ?>
<?php include __DIR__ . '/modal-hapus.php'; ?>
<?php include __DIR__ . '/modal-cetak.php'; ?>

