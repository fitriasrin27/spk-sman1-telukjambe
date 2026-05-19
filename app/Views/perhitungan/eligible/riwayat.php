<?php if (!$hasilDipilih): ?>
<!-- Riwayat Perhitungan Eligible -->
<div class="card border-0 shadow-sm mb-4" id="cardRiwayatEligible">
    <!-- Header Card -->
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
        <strong class="fs-6"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Perhitungan Eligible</strong>
    </div>

    <!-- Body Card -->
    <div class="card-body">
        <!-- Control Panel -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small mb-0">Show</label>
                <!-- Form Limit -->
                <form method="GET" action="<?= e(url('')) ?>" id="formLimitEligible">
                    <input type="hidden" name="url" value="perhitungan/eligible">
                    <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran) ?>">
                    <input type="hidden" name="jurusan" value="<?= e($jurusan) ?>">
                    <select name="limit" class="form-select form-select-sm d-inline-block w-auto"
                            onchange="document.getElementById('formLimitEligible').submit()">
                        <?php foreach ([10, 25, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <label class="text-muted small mb-0">entries</label>
            </div>
            <!-- Tombol Hapus -->
            <?php if (!in_array(current_user()['role'], ['tu', 'wakasek', 'kepala_sekolah'])): ?>
            <button type="button" class="btn btn-outline-danger btn-sm fw-medium px-3" id="btnModeHapus" <?= empty($riwayat) ? 'disabled' : '' ?>>
                <i class="bi bi-trash me-1"></i>Hapus Data
            </button>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 small">
                <!-- Table Header -->
                <thead class="table-light text-center">
                    <tr>
                        <!-- Checkbox -->
                        <th width="40" class="text-center col-checkbox d-none">
                            <div class="d-flex justify-content-center">
                                <input type="checkbox" class="form-check-input m-0" id="selectAll">
                            </div>
                        </th>
                        <th style="width:45px;">No</th>
                        <th style="min-width:130px;">Tahun Ajaran</th>
                        <th style="min-width:110px;">Jurusan</th>
                        <th style="min-width:150px;">Tanggal Hitung</th>
                        <th style="width:120px;">Jumlah Siswa</th>
                        <th style="width:350px;">Dibuat Oleh</th>
                        <th style="width:330px;" class="col-aksi">Aksi</th>
                    </tr>
                </thead>
                <!-- Table Body -->
                <tbody>
                    <?php if (!empty($riwayat)): ?>
                        <?php foreach ($riwayat as $i => $r): ?>
                            <?php $no = $offset + $i + 1; ?>
                            <!-- Riwayat Row -->
                            <tr class="<?= $idPerhitungan === (int)$r['id_perhitungan'] ? 'table-primary' : '' ?> riwayat-row" data-id="<?= $r['id_perhitungan'] ?>">
                                <!-- Checkbox -->
                                <td class="text-center col-checkbox d-none">
                                    <div class="d-flex justify-content-center">
                                        <input type="checkbox" class="form-check-input item-checkbox m-0" value="<?= e($r['id_perhitungan']); ?>">
                                    </div>
                                </td>
                                <!-- No -->
                                <td class="text-center"><?= $no ?></td>
                                <!-- Tahun Ajaran -->
                                <td class="text-center"><?= e($r['tahun_ajaran']) ?></td>
                                <!-- Jurusan -->
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1">
                                        <?= e($r['jurusan']) ?>
                                    </span>
                                </td>
                                <!-- Tanggal Hitung -->
                                <td class="text-center"><?= tglId($r['tanggal_hitung'], $bulanId) ?></td>
                                <!-- Jumlah Siswa -->
                                <td class="text-center"><?= e($r['jumlah_siswa']) ?></td>
                                <!-- Dibuat Oleh -->
                                <td>
                                    <?php if (!empty($r['nama_user'])): ?>
                                        <!-- Get Role Badge -->
                                        <?php
                                        $rb = $roleBadge[$r['role_user']] ?? ['label' => $r['role_user'], 'style' => 'background:#6c757d;color:#fff;'];
                                        $initials = '';
                                        foreach (explode(' ', $r['nama_user']) as $w) $initials .= strtoupper(substr($w, 0, 1));
                                        $initials = substr($initials, 0, 2);
                                        ?>
                                        <!-- User Info -->
                                        <div class="d-flex align-items-center gap-2">
                                            <!-- User Photo -->
                                            <?php if (!empty($r['foto_user'])): ?>
                                                <img src="<?= asset('public/uploads/profile/' . $r['foto_user']) ?>" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;border:1.5px solid #dee2e6;" alt="">
                                            <?php else: ?>
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold" style="width:28px;height:28px;font-size:0.6rem;flex-shrink:0;"><?= $initials ?></div>
                                            <?php endif; ?>
                                            <!-- User Name and Role -->
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="small fw-medium text-nowrap"><?= e($r['nama_user']) ?></span>
                                                <span style="font-size:0.55rem;padding:1px 5px;border-radius:4px;white-space:nowrap;font-weight:600;<?= $rb['style'] ?>"><?= $rb['label'] ?></span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Aksi -->
                                <td class="text-center col-aksi">
                                    <!-- Get URL -->
                                    <?php
                                    $lihatUrl = url('perhitungan/eligible')
                                        . '&tahun_ajaran=' . urlencode($r['tahun_ajaran'])
                                        . '&jurusan='      . urlencode($r['jurusan'])
                                        . '&id='           . $r['id_perhitungan'];
                                    $detailUrl = url('perhitungan/eligible-detail') . '&id=' . $r['id_perhitungan'];
                                    ?>

                                    <!-- Buttons -->
                                    <div class="d-flex justify-content-center align-items-center">
                                        <!-- Lihat Peringkat -->
                                        <a href="<?= e($lihatUrl) ?>" class="btn btn-sm btn-outline-primary px-1" style="font-size:0.75rem;">
                                            <i class="bi bi-trophy me-1"></i>Lihat Peringkat
                                        </a>
                                        <span class="aksi-separator"></span>
                                        <!-- Detail SAW -->
                                        <a href="<?= e($detailUrl) ?>" class="btn btn-sm btn-outline-info px-1" style="font-size:0.75rem;">
                                            <i class="bi bi-table me-1"></i>Detail SAW
                                        </a>
                                        <!-- Delete Button -->
                                        <?php if (!in_array(current_user()['role'], ['tu', 'wakasek', 'kepala_sekolah'])): ?>
                                        <span class="aksi-separator"></span>
                                        <!-- Konfirmasi Hapus -->
                                        <button type="button" class="btn btn-sm btn-outline-danger px-2"
                                                onclick="konfirmasiHapusEligible(<?= $r['id_perhitungan'] ?>, 'Angkatan <?= e($r['tahun_ajaran']) ?> &bull; <?= e($r['jurusan']) ?>', '<?= tglId($r['tanggal_hitung'], $bulanId) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Empty Table -->
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-clock-history fs-2 d-block mb-2 opacity-50"></i>
                                <?php if ($tahunAjaran || $jurusan): ?>
                                    Belum ada riwayat perhitungan untuk filter ini.
                                <?php else: ?>
                                    Belum ada riwayat perhitungan. Pilih filter dan klik <strong>Hitung Peringkat</strong>.
                                <?php endif; ?>
                            </td>
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
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($baseUrl . '&page=' . ($page - 1)) ?>">‹</a>
                    </li>
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <?php if ($p === 1 || $p === $totalPages || abs($p - $page) <= 1): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="<?= e($baseUrl . '&page=' . $p) ?>"><?= $p ?></a>
                            </li>
                        <?php elseif (abs($p - $page) === 2): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= e($baseUrl . '&page=' . ($page + 1)) ?>">›</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
