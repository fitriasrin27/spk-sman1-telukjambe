<?php
// Hitung kuota jika belum ada (dari data riwayat yang disimpan)
$_kuota = $kuotaEligible ?? 0;
if (!$_kuota && !empty($riwayat['jurusan']) && !empty($riwayat['tahun_ajaran'])) {
    // Fallback: estimasi dari total hasil
    $_kuota = (int)round($total * 0.40);
}
?>
<!-- HASIL PERINGKAT -->
<div class="card border-0 shadow-sm" id="hasilPeringkat">
    <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
        <div>
            <!-- Title -->
            <strong class="fs-6"><i class="bi bi-award text-warning me-2"></i>Hasil Perhitungan Peringkat Eligible</strong>
            <!-- Badge Jurusan dan Tahun Ajaran -->
            <span class="ms-2 badge bg-success-subtle text-success border border-success-subtle">
                <?= e($riwayat['jurusan']) ?> &bull;
                <?= e($riwayat['tahun_ajaran']) ?>
            </span>
            <!-- Info Tanggal Hitung -->
            <small class="text-muted ms-2" style="font-size:0.7rem;">
                Dihitung: <?= tglId($riwayat['tanggal_hitung'], $bulanId) ?>
            </small>
        </div>
        <div class="d-flex gap-2">
            <!-- Tombol Kembali -->
            <a href="<?= e($baseUrl) ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Riwayat
            </a>
            <!-- Tombol Detail SAW -->
            <a href="<?= e(url('perhitungan/eligible-detail') . '&id=' . $riwayat['id_perhitungan']) ?>"
               class="btn btn-outline-info btn-sm">
                <i class="bi bi-table me-1"></i>Detail SAW
            </a>
            <!-- Tombol Export PDSS -->
            <button type="button" class="btn btn-outline-success btn-sm px-3 shadow-sm fw-medium" data-bs-toggle="modal" data-bs-target="#modalExportPDSS">
                <i class="bi bi-file-earmark-excel me-1"></i>Export PDSS
            </button>
            <!-- Tombol Lihat Hasil Akhir -->
            <a href="<?= e(url('hasil/eligible-lihat') . '&id=' . $idPerhitungan) ?>" class="btn btn-outline-lilac btn-sm px-3 shadow-sm fw-medium">
                <i class="bi bi-bar-chart-line me-1"></i>Lihat Hasil Akhir
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Info Kuota -->
        <?php if ($_kuota > 0): ?>
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3 p-2 rounded-3" style="background:#f8f9fa;border:1px solid #e9ecef;">
            <!-- Info Kuota Lolos -->
            <span class="d-flex align-items-center gap-1">
                <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#198754;"></span>
                <span class="small">Peringkat 1–<span><?= $_kuota ?></span>: <strong class="text-success">Lolos Eligible SNBP</strong> (<?= $_kuota ?> siswa)</span>
            </span>
            <span class="text-muted small mx-1">|</span>
            <!-- Info Kuota Tidak Lolos -->
            <span class="d-flex align-items-center gap-1">
                <span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#dc3545;"></span>
                <span class="small">Peringkat <?= $_kuota + 1 ?>+: <strong class="text-danger">Tidak Lolos</strong></span>
            </span>
            <!-- Info Total Pendaftar dan Kuota -->
            <span class="ms-auto small text-muted">Total pendaftar: <strong><?= $total ?></strong> | Kuota: <strong class="text-success"><?= $_kuota ?></strong></span>
        </div>
        <?php endif; ?>

        <!-- Toolbar: Show Entries & Search -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <!-- Info Show Entries -->
            <div class="d-flex align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">Show</label>
                    <!-- Form Show Entries -->
                    <form method="GET" action="<?= e(url('')) ?>" id="formLimitHasil">
                        <input type="hidden" name="url" value="perhitungan/eligible">
                        <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran) ?>">
                        <input type="hidden" name="jurusan" value="<?= e($jurusan) ?>">
                        <input type="hidden" name="id" value="<?= e($idPerhitungan) ?>">
                        <input type="hidden" name="q" value="<?= e($search) ?>">
                        <select name="limit" class="form-select form-select-sm d-inline-block w-auto"
                                onchange="document.getElementById('formLimitHasil').submit()">
                            <?php foreach ([10, 25, 50, 100] as $opt): ?>
                                <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <label class="text-muted small mb-0">entries</label>
                </div>
            </div>

            <div class="ms-auto" style="min-width: 250px;">
                <!-- Form Search -->
                <form method="GET" action="<?= e(url('')) ?>" id="formSearchHasil">
                    <input type="hidden" name="url" value="perhitungan/eligible">
                    <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran) ?>">
                    <input type="hidden" name="jurusan" value="<?= e($jurusan) ?>">
                    <input type="hidden" name="id" value="<?= e($idPerhitungan) ?>">
                    <input type="hidden" name="limit" value="<?= e($limit) ?>">
                    <!-- Input Search -->
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

        <!-- Tabel Hasil Peringkat -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 small">
                <thead class="table-light text-center">
                    <!-- Baris 1: Rank, Status, Nama Lengkap, NISN, NIS, Kelas -->
                    <tr>
                        <th rowspan="2" class="align-middle py-2" style="width:50px;">Rank</th>
                        <th rowspan="2" class="align-middle py-2" style="width:110px;">Status</th>
                        <th rowspan="2" class="align-middle py-2" style="min-width:180px;">Nama Lengkap</th>
                        <th rowspan="2" class="align-middle py-2">NISN</th>
                        <th rowspan="2" class="align-middle py-2">NIS</th>
                        <th rowspan="2" class="align-middle py-2">Kelas</th>
                        <!-- Kolom Nilai Normalisasi -->
                        <th colspan="4" class="py-2">Nilai Normalisasi</th>
                        <!-- Kolom Nilai Preferensi -->
                        <th rowspan="2" class="align-middle py-2">Nilai Preferensi</th>
                        <!-- Kolom Rata-Rata Nilai -->
                        <th rowspan="2" class="align-middle py-2">Rata-Rata Nilai</th>
                    </tr>
                    <!-- Baris 2: Nilai, Absensi, Ekskul, Prestasi -->
                    <tr>
                        <th class="py-1" style="box-shadow: -1px 0 0 0 #dee2e6;">Nilai</th>
                        <th class="py-1">Absensi</th>
                        <th class="py-1">Ekskul</th>
                        <th class="py-1">Prestasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($hasil)): ?>
                        <?php
                        $prevLolos = true; // track apakah baris sebelumnya lolos
                        foreach ($hasil as $row):
                            // Prioritas status_eligible dari DB, fallback ke perhitungan ranking
                            $lolos = ($row['status_eligible'] !== null) 
                                ? ($row['status_eligible'] === 'ya') 
                                : ($_kuota > 0 && $row['ranking'] <= $_kuota);

                            // Tambah separator saat beralih dari lolos ke tidak lolos
                            if (!$lolos && $prevLolos && $_kuota > 0):
                            ?>
                                <!-- Baris Pemisah Batas Kuota -->
                                <tr class="batas-kuota">
                                    <td colspan="12" class="py-1 text-center" style="background: rgba(255, 193, 7, 0.05); border: 2px dashed rgba(255, 193, 7, 0.5);">
                                        <small class="text-warning fw-semibold">
                                            <i class="bi bi-scissors me-1"></i>
                                            Batas Kuota — Peringkat <?= $_kuota + 1 ?> ke bawah tidak mendapat kuota SNBP
                                        </small>
                                    </td>
                                </tr>
                            <?php 
                            endif; 
                            $prevLolos = $lolos; 

                            $rowStyle = $lolos 
                                ? 'background-color: rgba(25, 135, 84, 0.07); border-left: 4px solid #198754;' 
                                : 'background-color: rgba(220, 53, 69, 0.07); border-left: 4px solid #dc3545;';
                            ?>
                            <!-- Baris Hasil Peringkat -->
                            <tr style="<?= $rowStyle ?>">
                                <!-- Kolom Rank (medal untuk 3 teratas, nomor biasa untuk lainnya) -->
                                <td class="text-center" style="<?= $rowStyle ?>">
                                    <?php if ($row['ranking'] <= 3 && $lolos): ?>
                                        <span class="badge <?= $row['ranking'] == 1 ? 'bg-warning text-dark' : ($row['ranking'] == 2 ? 'bg-secondary' : 'bg-bronze') ?>">
                                            <i class="bi bi-award me-1"></i><?= $row['ranking'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="small fw-semibold <?= $lolos ? 'text-success' : 'text-danger' ?>"><?= $row['ranking'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <!-- Kolom Status -->
                                <td class="text-center" style="<?= $rowStyle ?>">
                                    <?php if ($lolos): ?>
                                        <span class="badge bg-success" style="font-size:0.7rem;">
                                            <i class="bi bi-check-circle me-1"></i>Eligible
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger" style="font-size:0.7rem;">
                                            <i class="bi bi-x-circle me-1"></i>Tidak Lolos
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-start fw-medium" style="<?= $rowStyle ?>"><?= e($row['nama']) ?></td>
                                <td class="text-center text-dark" style="<?= $rowStyle ?>"><?= e($row['nisn']) ?></td>
                                <td class="text-center text-dark" style="<?= $rowStyle ?>"><?= e($row['nis']) ?></td>
                                <td class="text-center" style="<?= $rowStyle ?>"><?= e($row['kelas'] ?: '-') ?></td>
                                <!-- Kolom Nilai Normalisasi -->
                                <td class="text-center" style="<?= $rowStyle ?>"><?= number_format($row['n_c1'], 4) ?></td>
                                <td class="text-center" style="<?= $rowStyle ?>"><?= number_format($row['n_c2'], 4) ?></td>
                                <td class="text-center" style="<?= $rowStyle ?>"><?= number_format($row['n_c3'], 4) ?></td>
                                <td class="text-center" style="<?= $rowStyle ?>"><?= number_format($row['n_c4'], 4) ?></td>
                                <!-- Kolom Nilai Preferensi -->
                                <td class="text-center fw-bold <?= $lolos ? 'text-success' : 'text-danger' ?>" style="<?= $rowStyle ?>"><?= number_format($row['nilai_preferensi'], 6) ?></td>
                                <!-- Kolom Rata-Rata Nilai -->
                                <td class="text-center" style="<?= $rowStyle ?>"><?= number_format($row['rata_rata_akademik'], 4) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Baris Kosong -->
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">Tidak ada data hasil perhitungan.</td>
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
                <?php if ($_kuota > 0): ?>
                &nbsp;|&nbsp;
                <span class="text-success fw-medium"><?= min($_kuota, $total) ?> Eligible</span> &nbsp;
                <span class="text-danger fw-medium"><?= max(0, $total - $_kuota) ?> Tidak Lolos</span>
                <?php endif; ?>
            </div>
            <!-- Tengah: pagination (benar-benar di tengah) -->
            <?php if ($totalPages > 1): ?>
            <nav style="position: absolute; left: 50%; transform: translateX(-50%);">
                <?php $baseUrlHasil = $baseUrl . '&id=' . $idPerhitungan . '&q=' . urlencode($search); ?>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($baseUrlHasil . '&page=' . ($page - 1)) ?>">‹</a>
                    </li>
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <?php if ($p === 1 || $p === $totalPages || abs($p - $page) <= 1): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= e($baseUrlHasil . '&page=' . $p) ?>"><?= $p ?></a>
                            </li>
                        <?php elseif (abs($p - $page) === 2): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($baseUrlHasil . '&page=' . ($page + 1)) ?>">›</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
