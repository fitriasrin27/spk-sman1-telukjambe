<?php if ($hasilDipilih): ?>
<!-- Style dipindahkan ke perhitungan.css -->

<div class="card border-0 shadow-sm" id="hasilPeringkat">
    <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
        <div>
            <!-- Judul Hasil Perhitungan Peringkat Kelas -->
            <strong class="fs-6"><i class="bi bi-award text-warning me-2"></i>Hasil Perhitungan Peringkat Kelas</strong>
            <!-- Label Semester -->
            <span class="ms-2 badge bg-success-subtle text-success border border-success-subtle">
                <?= e($hasilDipilih['batch']['kelas']) ?> &bull;
                <?= e($labelSemester[$hasilDipilih['batch']['semester_target']] ?? 'Sem '.$hasilDipilih['batch']['semester_target']) ?> &bull;
                <?= e($hasilDipilih['batch']['tahun_ajaran']) ?>
            </span>
            <!-- Label Tanggal Hitung -->
            <small class="text-muted ms-2" style="font-size:0.7rem;">
                Dihitung: <?= tglId($hasilDipilih['batch']['tanggal_hitung'], $bulanId) ?>
            </small>
        </div>
        <div class="d-flex gap-2">
            <!-- Tombol Kembali -->
            <a href="<?= e($baseUrl) ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Riwayat
            </a>
            <!-- Tombol Detail SAW -->
            <a href="<?= e(url('perhitungan/detail-kelas') . '&id=' . $idPerhitungan) ?>"
               class="btn btn-outline-info btn-sm">
                <i class="bi bi-table me-1"></i>Detail SAW
            </a>
            <!-- Tombol Lihat Hasil Akhir -->
            <a href="<?= e(url('hasil/kelas-lihat') . '&id=' . $idPerhitungan) ?>" class="btn btn-outline-lilac btn-sm px-3 shadow-sm fw-medium">
                <i class="bi bi-bar-chart-line me-1"></i>Lihat Hasil Akhir
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <!-- Dropdown Jumlah Tampil -->
                <label class="text-muted small mb-0">Show</label>
                <form method="GET" action="<?= e(url('')) ?>" id="formLimitHasil">
                    <input type="hidden" name="url" value="perhitungan/kelas">
                    <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran) ?>">
                    <input type="hidden" name="kelas" value="<?= e($kelas) ?>">
                    <input type="hidden" name="semester" value="<?= e($semester) ?>">
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
            <!-- Search Box -->
            <div class="ms-auto" style="min-width: 250px;">
                <form method="GET" action="<?= e(url('')) ?>" id="formSearchHasil">
                    <input type="hidden" name="url" value="perhitungan/kelas">
                    <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran) ?>">
                    <input type="hidden" name="kelas" value="<?= e($kelas) ?>">
                    <input type="hidden" name="semester" value="<?= e($semester) ?>">
                    <input type="hidden" name="id" value="<?= e($idPerhitungan) ?>">
                    <input type="hidden" name="limit" value="<?= e($limit) ?>">
                    <!-- Form Search -->
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

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 small">
                <!-- Table Header -->
                <thead class="table-light text-center">
                    <tr>
                        <th rowspan="2" class="align-middle py-2" style="width:50px;">Rank</th>
                        <th rowspan="2" class="align-middle py-2" style="min-width:180px;">Nama Lengkap</th>
                        <th rowspan="2" class="align-middle py-2">NISN</th>
                        <th rowspan="2" class="align-middle py-2">NIS</th>
                        <th colspan="4" class="py-2">Nilai Normalisasi</th>
                        <th rowspan="2" class="align-middle py-2">Nilai Preferensi</th>
                        <th rowspan="2" class="align-middle py-2">Rata-Rata Nilai</th>
                    </tr>
                    <tr>
                        <th class="py-1" style="box-shadow: -1px 0 0 0 #dee2e6;">Nilai</th>
                        <th class="py-1">Absensi</th>
                        <th class="py-1">Ekskul</th>
                        <th class="py-1">Prestasi</th>
                    </tr>
                </thead>
                <!-- Table Body -->
                <tbody>
                    <!-- Check if results exist -->
                    <?php if (!empty($hasilDipilih['rows'])): ?>
                        <!-- Loop through results -->
                        <?php foreach ($hasilDipilih['rows'] as $row): ?>
                            <!-- Table Rows -->
                            <tr>
                                <td class="text-center">
                                    <!-- Check for top 3 -->
                                    <?php if ($row['ranking'] <= 3): ?>
                                        <span class="badge <?= ['bg-warning text-light','bg-secondary','badge-bronze'][($row['ranking']-1)] ?>">
                                            <i class="bi bi-award me-1"></i><?= $row['ranking'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="small"><?= $row['ranking'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <!-- Nama, NISN, NIS -->
                                <td><?= e($row['nama']) ?></td>
                                <td class="text-center"><?= e($row['nisn']) ?></td>
                                <td class="text-center"><?= e($row['nis']) ?></td>
                                <!-- Nilai Normalisasi -->
                                <td class="text-center"><?= number_format($row['n_c1'], 4) ?></td>
                                <td class="text-center"><?= number_format($row['n_c2'], 4) ?></td>
                                <td class="text-center"><?= number_format($row['n_c3'], 4) ?></td>
                                <td class="text-center"><?= number_format($row['n_c4'], 4) ?></td>
                                <!-- Nilai Preferensi -->
                                <td class="text-center fw-bold text-primary"><?= number_format($row['nilai_preferensi'], 6) ?></td>
                                <!-- Rata-Rata Nilai -->
                                <td class="text-center"><?= number_format($row['rata_rata_akurat'] ?? $row['rata_rata_akademik'], 4) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <!-- No Data -->
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Tidak ada data.</td>
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
            </div>
            <!-- Tengah: pagination (benar-benar di tengah) -->
            <?php if ($totalPages > 1): ?>
            <nav style="position: absolute; left: 50%; transform: translateX(-50%);">
                <?php 
                $baseUrlHasil = $baseUrl . '&id=' . $idPerhitungan . '&q=' . urlencode($search); 
                ?>
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
<?php endif; ?>
