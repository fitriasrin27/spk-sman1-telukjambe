<?php
[$active, $subActive] = resolveMenu();
$bulanId = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
// Fungsi Format tanggal
function tglIdTime($datetimeStr, $bulanId) {
    if (!$datetimeStr) return '-';
    $time = strtotime($datetimeStr);
    $tgl = date('d', $time);
    $bln = $bulanId[(int)date('n', $time)];
    $thn = date('Y', $time);
    $jam = date('H:i:s', $time);
    return "$tgl $bln $thn, $jam";
}
// Badge Role
$roleBadge = [
    'admin'     => ['label' => 'ADM', 'style' => 'background:#e0f2fe;color:#0369a1;'],
    'operator'  => ['label' => 'OP',  'style' => 'background:#ede9fe;color:#5b21b6;'],
    'kepsek'    => ['label' => 'KS',  'style' => 'background:#ffedd5;color:#c2410c;'],
    'wali_kelas'=> ['label' => 'WK',  'style' => 'background:#dcfce7;color:#15803d;']
];
?>

<!-- Judul Halaman -->
<div class="page-header">
    <h2 class="page-title"><?= e($title) ?></h2>
</div>

<!-- Filter Section -->
<div class="mb-4">
    <form method="GET" action="<?= e(url('')) ?>">
        <input type="hidden" name="url" value="laporan">
        <div class="row g-2 align-items-end">

            <div class="col-auto">
                <!-- Tahun Ajaran -->
                <select name="tahun_ajaran" class="form-select form-select-sm" style="min-width:160px;">
                    <option value="">Tahun Ajaran</option>
                    <?php foreach ($optTahunAjaran as $ta): ?>
                        <option value="<?= e($ta) ?>" <?= $tahunAjaran === $ta ? 'selected' : '' ?>><?= e($ta) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-auto">
                <!-- Jenis Laporan -->
                <select name="jenis_laporan" class="form-select form-select-sm" style="min-width:160px;">
                    <option value="">Jenis Laporan</option>
                    <option value="kelas" <?= $jenisLaporan === 'kelas' ? 'selected' : '' ?>>Peringkat Kelas</option>
                    <option value="eligible" <?= $jenisLaporan === 'eligible' ? 'selected' : '' ?>>Peringkat Eligible</option>
                </select>
            </div>
            <div class="col-auto">
                <!-- Tombol Tampilkan -->
                <button type="submit" class="btn btn-warning btn-sm text-white px-3 fw-medium shadow-sm">
                    <i class="bi bi-search me-1"></i>Tampilkan
                </button>
            </div>
            <?php if ($tahunAjaran || $jenisLaporan): ?>
            <!-- Tombol Reset -->
            <div class="col-auto">
                <a href="<?= e(url('laporan')) ?>" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                </a>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Card Daftar Riwayat Laporan -->
<div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <!-- Title -->
            <strong class="fs-6"><i class="bi bi-clock-history text-primary me-2"></i>Daftar Riwayat Laporan</strong>
            <?php if (!in_array(current_user()['role'], ['kepala_sekolah', 'wakasek'])): ?>
            <!-- Tombol Hapus Data -->
            <button type="button" class="btn btn-outline-danger btn-sm fw-medium px-3" id="btnModeHapus" <?= empty($laporan) ? 'disabled' : '' ?>>
                <i class="bi bi-trash me-1"></i>Hapus Data
            </button>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <!-- Toolbar Tampilan & Filter -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">Show</label>
                    <select class="form-select form-select-sm d-inline-block w-auto" 
                            onchange="window.location.href='<?= url('laporan') ?>&tahun_ajaran=<?= urlencode($tahunAjaran) ?>&jenis_laporan=<?= urlencode($jenisLaporan) ?>&limit='+this.value">
                        <?php foreach ([10, 25, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="text-muted small mb-0">entries</label>
                </div>
            </div>
            <!-- Tabel Riwayat Laporan -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 small">
                    <thead class="table-light text-center">
                        <!-- Baris header tabel -->
                        <tr>
                            <!-- Checkbox -->
                            <th width="40" class="text-center col-checkbox d-none">
                                <div class="d-flex justify-content-center">
                                    <input type="checkbox" class="form-check-input m-0" id="selectAllLaporan">
                                </div>
                            </th>
                            <th style="width:45px;">No</th>
                            <th style="min-width:180px;">Tanggal Pembuatan</th>
                            <th style="min-width:140px;">Jenis Peringkat</th>
                            <th style="min-width:110px;">Tahun Ajaran</th>
                            <th style="min-width:140px;">Kelas / Jurusan</th>
                            <th style="min-width:250px;">Dibuat Oleh</th>
                            <th style="width:300px;" class="col-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <!-- Baris isi tabel -->
                    <tbody>
                        <?php if (!empty($laporan)): ?>
                            <?php foreach ($laporan as $i => $l): 
                                // Baris untuk tabel riwayat laporan
                                if ($l['jenis_laporan'] === 'kelas') {
                                    $badgeClass = 'bg-info-subtle text-info';
                                    $label = 'Peringkat Kelas';
                                    $ta = $l['tahun_ajaran'];
                                    $subLabel = $l['batch_kelas'];
                                } elseif ($l['jenis_laporan'] === 'leger') {
                                    $badgeClass = 'bg-warning-subtle text-warning';
                                    $label = 'Leger Nilai';
                                    $meta = json_decode($l['komponen_laporan'], true);

                                    // Logika untuk leger nilai
                                    if (!empty($meta)) {
                                        $ta = $meta['ta'] ?? '—';
                                        $subLabel = ($meta['kelas'] ?? '—') . ' (' . ucfirst($meta['sem'] ?? '') . ')';
                                    } else {
                                        // Fallback pintar: Ambil dari nama file (Leger-Nilai_TA_KELAS_SEM_...)
                                        $filename = basename($l['file_path']);
                                        $parts = explode('_', $filename);
                                        // Parts: [0]Leger-Nilai, [1]TA, [2]Kelas, [3]Sem
                                        $ta = isset($parts[1]) ? str_replace('-', '/', $parts[1]) : '—';
                                        $rawKelas = isset($parts[2]) ? str_replace('-', ' ', $parts[2]) : '—';
                                        $rawSem   = isset($parts[3]) ? ucfirst($parts[3]) : '';
                                        $subLabel = $rawKelas . ($rawSem ? " ($rawSem)" : "");
                                    }
                                } else {
                                    // Logika untuk peringkat eligible
                                    $badgeClass = 'bg-success-subtle text-success';
                                    $label = 'Peringkat Eligible';
                                    $ta = $l['tahun_ajaran'];
                                    $subLabel = $l['jurusan'];
                                }
                                
                                // Warna badge untuk role user
                                $roleBadgeColors = [
                                    'operator'        => ['label' => 'OP',      'style' => 'background:#cfe2ff;color:#084298;'],
                                    'wali_kelas'      => ['label' => 'Walas',   'style' => 'background:#d1e7dd;color:#0a3622;'],
                                    'bk'              => ['label' => 'BK',      'style' => 'background:#e8d5ff;color:#432874;'],
                                    'tu'              => ['label' => 'TU',      'style' => 'background:#ffe5d0;color:#7c3c00;'],
                                    'wakasek'         => ['label' => 'Wakasek', 'style' => 'background:#cff4fc;color:#055160;'],
                                    'kepala_sekolah'  => ['label' => 'Kepsek',  'style' => 'background:#f8d7da;color:#842029;'],
                                ];
                            ?>
                                <!-- Baris data laporan -->
                                <tr class="riwayat-row">
                                    <!-- Checkbox (sembunyi jika user role BK/Walas) -->
                                    <td class="text-center col-checkbox d-none">
                                        <div class="d-flex justify-content-center">
                                            <?php 
                                            $isRestrictedBK = (current_user()['role'] === 'bk' && in_array($l['jenis_laporan'], ['kelas', 'leger']));
                                            $isRestrictedWalas = (current_user()['role'] === 'wali_kelas' && $l['jenis_laporan'] === 'eligible');
                                            $isDisabled = $isRestrictedBK || $isRestrictedWalas;
                                            $titleAttr = $isRestrictedBK ? 'BK tidak diizinkan menghapus laporan ini' : ($isRestrictedWalas ? 'Wali Kelas tidak diizinkan menghapus laporan ini' : '');
                                            ?>
                                            <input class="form-check-input row-checkbox-laporan m-0" type="checkbox" name="ids[]" value="<?= $l['id_laporan'] ?>" <?= $isDisabled ? 'disabled title="' . $titleAttr . '"' : '' ?>>
                                        </div>
                                    </td>
                                    <!-- No -->
                                    <td class="text-center text-muted"><?= $offset + $i + 1 ?></td>
                                    <!-- Tanggal buat -->
                                    <td class="text-center text-dark">
                                        <?= tglIdTime($l['tanggal_buat'], $bulanId) ?>
                                    </td>
                                    <!-- Jenis laporan -->
                                    <td class="text-center">
                                        <span class="badge <?= $badgeClass ?> border border-opacity-10 py-1 px-3">
                                            <?= $label ?>
                                        </span>
                                    </td>
                                    <!-- Tahun Ajaran -->
                                    <td class="text-center text-dark"><?= e($ta) ?></td>
                                    <!-- Jurusan/Semester -->
                                    <td class="text-center text-dark">
                                        <?= e($subLabel) ?>
                                    </td>
                                    <!-- Dibuat Oleh -->
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <!-- Badge untuk role user -->
                                            <?php
                                            $rb = $roleBadgeColors[$l['role_user'] ?? ''] ?? ['label' => '??', 'style' => 'background:#6c757d;color:#fff;'];
                                            $initials = '';
                                            foreach (explode(' ', $l['nama_user']) as $w) $initials .= strtoupper(substr($w, 0, 1));
                                            $initials = substr($initials, 0, 2);
                                            ?>
                                            <!-- Baris untuk foto user -->
                                            <?php if (!empty($l['foto_user'])): ?>
                                                <img src="<?= asset('public/uploads/profile/' . $l['foto_user']) ?>" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;border:1.5px solid #dee2e6;" alt="">
                                            <?php else: ?>
                                                <!-- Baris untuk inisial user -->
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold" style="width:28px;height:28px;font-size:0.6rem;flex-shrink:0;"><?= $initials ?></div>
                                            <?php endif; ?>
                                            <!-- Baris untuk nama user -->
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="small fw-medium text-nowrap text-dark"><?= e($l['nama_user']) ?></span>
                                                <span style="font-size:0.55rem;padding:1px 5px;border-radius:4px;white-space:nowrap;font-weight:600;<?= $rb['style'] ?>"><?= $rb['label'] ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Tombol Aksi -->
                                    <td class="text-center col-aksi">
                                        <?php $lihatUrl = url('laporan/detail') . '&id=' . $l['id_laporan']; ?>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <!-- Tombol Lihat Detail -->
                                            <a href="<?= e($lihatUrl) ?>" class="btn btn-sm btn-outline-info px-1 btn-lihat" style="font-size:0.75rem;">
                                                <i class="bi bi-eye me-1"></i>Lihat Detail
                                            </a>
                                            <span class="aksi-separator"></span>
                                            <!-- Tombol Unduh -->
                                            <a href="<?= e(url('laporan/download')) . '&id=' . $l['id_laporan'] ?>" target="_blank" class="btn btn-sm btn-outline-success px-1 btn-unduh" style="font-size:0.75rem;">
                                                <i class="bi bi-download me-1"></i>Unduh
                                            </a>
                                        
                                            <!-- Pengaturan Tombol Hapus (BK gabisa hapus kelas & leger, Walas gabisa hapus eligible) -->
                                            <?php 
                                            $isRestrictedBK = (current_user()['role'] === 'bk' && in_array($l['jenis_laporan'], ['kelas', 'leger']));
                                            $isRestrictedWalas = (current_user()['role'] === 'wali_kelas' && $l['jenis_laporan'] === 'eligible');
                                            if (!in_array(current_user()['role'], ['wakasek', 'kepala_sekolah']) && !$isRestrictedBK && !$isRestrictedWalas): 
                                            ?>
                                            <span class="aksi-separator"></span>
                                            <!-- Tombol Hapus -->
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 btn-hapus" onclick="confirmDeleteLaporan(<?= $l['id_laporan'] ?>)" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Baris jika tidak ada laporan -->
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-x d-block mb-2 fs-2"></i>
                                    Belum ada riwayat laporan yang ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer tabel: info kiri, pagination tengah -->
            <?php if ($total > 0): ?>
            <div class="d-flex align-items-center mt-3" style="position: relative; min-height: 32px;">
                <!-- Info jumlah baris -->
                <div class="text-muted small">
                    Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $total) ?> of <?= $total ?> entries
                </div>
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav style="position: absolute; left: 50%; transform: translateX(-50%);">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= url('laporan') ?>&tahun_ajaran=<?= urlencode($tahunAjaran) ?>&jenis_laporan=<?= urlencode($jenisLaporan) ?>&limit=<?= $limit ?>&page=<?= $page - 1 ?>">Previous</a>
                        </li>
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= ($p === $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= url('laporan') ?>&tahun_ajaran=<?= urlencode($tahunAjaran) ?>&jenis_laporan=<?= urlencode($jenisLaporan) ?>&limit=<?= $limit ?>&page=<?= $p ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= url('laporan') ?>&tahun_ajaran=<?= urlencode($tahunAjaran) ?>&jenis_laporan=<?= urlencode($jenisLaporan) ?>&limit=<?= $limit ?>&page=<?= $page + 1 ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
<!-- Toolbar & Modal Hapus -->
<?php include __DIR__ . '/hapus.php'; ?>


<link rel="stylesheet" href="<?= e(asset('assets/css/pages/laporan.css')); ?>">
<script>
    /* URL diekspos ke JS agar bisa dipakai oleh laporan.js eksternal */
    const laporanDeleteUrl = '<?= e(url('laporan/delete')) ?>';
</script>
<script src="<?= e(asset('assets/js/pages/laporan.js')); ?>"></script>

