<?php if ($tahunAjaran && $jurusan && current_user()['role'] !== 'tu'): ?>
<!-- Panel Seleksi Pendaftar Eligible -->
<div class="card border-0 shadow-sm mb-4" id="cardSeleksiPendaftar" style="display:none;">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
        <div>
            <!-- Title -->
            <strong class="fs-6">
                <i class="bi bi-person-check text-success me-2"></i>Pilih Peserta Seleksi SNBP
            </strong>
            <!-- Jurusan -->
            <span class="ms-2 badge bg-info-subtle text-info border border-info-subtle">
                <?= e($tahunAjaran) ?> &bull; <?= e($jurusan) ?>
            </span>
        </div>
        <!-- Close Button -->
        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnTutupSeleksi">
            <i class="bi bi-x-lg me-1"></i>Tutup
        </button>
    </div>

    <!-- Info Kuota -->
    <div class="px-4 pt-3">
        <div class="d-flex flex-wrap gap-3 align-items-center p-3 rounded-3"
             style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #bbf7d0;">
            <!-- Total Siswa -->
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-people-fill text-success fs-5"></i>
                <div>
                    <div class="small text-muted">Total Siswa <?= e($jurusan) ?></div>
                    <div class="fw-bold fs-6"><?= $totalSiswaJurusan ?> Siswa</div>
                </div>
            </div>
            <div class="vr opacity-25"></div>
            <!--Kuota Eligible -->
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-award-fill text-warning fs-5"></i>
                <div>
                    <div class="small text-muted">Kuota Eligible (40%)</div>
                    <div class="fw-bold fs-6 text-warning"><?= $kuotaEligible ?> Siswa</div>
                </div>
            </div>
            <div class="vr opacity-25"></div>
            <!-- Total Dipilih -->
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-ui-checks text-primary fs-5"></i>
                <div>
                    <div class="small text-muted">Total Dipilih</div>
                    <div class="fw-bold fs-6 text-primary" id="infoTotalDipilih">0 Siswa</div>
                </div>
            </div>
            <!-- Info Siswa Dipilih -->
            <div class="ms-auto">
                <div class="small text-muted text-end">Sistem akan meranking</div>
                <div class="small fw-semibold text-end">
                    <span id="infoTotalDipilih2">0</span> Peserta → ambil <span class="text-warning"><?= $kuotaEligible ?> terbaik</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Tab Kelas -->
        <?php if (!empty($daftarKelas)): ?>
        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
            <!-- Tampilkan Kelas -->
            <span class="text-muted small">Tampilkan kelas:</span>
            <!-- Semua Kelas Button -->
            <button type="button" class="btn btn-sm btn-primary btn-kelas active" data-kelas="">
                Semua Kelas
            </button>
            <!-- Daftar Kelas -->
            <?php foreach ($daftarKelas as $k): ?>
            <button type="button" class="btn btn-sm btn-outline-primary btn-kelas" data-kelas="<?= e($k) ?>">
                <?= e($k) ?>
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Toolbar: search + select all info -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <!-- Search Bar -->
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="width:240px;">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="searchSiswaEligible" class="form-control border-start-0 ps-0"
                           placeholder="Cari nama / NISN...">
                </div>
            </div>
            <!-- Info Siswa -->
            <div class="d-flex align-items-center gap-3">
                <div class="small text-muted">
                    Tampil: <strong id="infoTampil">0</strong> |
                    Dipilih kelas ini: <strong id="infoDipilihKelas">0</strong>
                </div>
            </div>
        </div>

        <!-- Tabel siswa -->
        <div class="table-responsive" style="max-height:420px;overflow-y:auto;">
            <table class="table table-bordered table-hover align-middle mb-0 small" id="tabelSiswaEligible">
                <thead class="table-light text-center" style="position:sticky;top:0;z-index:1;">
                    <tr>
                        <!-- No -->
                        <th style="width:40px;">#</th>
                        <!-- Checkbox -->
                        <th style="width:42px;">
                            <div class="d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" id="selectAllSiswa" title="Pilih Semua (tampil)">
                            </div>
                        </th>
                        <!-- Nama Siswa -->
                        <th style="min-width:180px;">Nama Siswa</th>
                        <!-- NISN -->
                        <th style="width:140px;">NISN</th>
                        <!-- Kelas -->
                        <th style="width:100px;">Kelas</th>
                        <!-- Status -->
                        <th style="width:120px;">Status</th>
                    </tr>
                </thead>
                <tbody id="tbodySiswaEligible">
                    <!-- Empty Table -->
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                            Memuat data siswa...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Form Submit -->
        <form method="POST" action="<?= e(url('perhitungan/eligible-hitung')) ?>" id="formHitungEligible" class="mt-3">
            <input type="hidden" name="tahun_ajaran" value="<?= e($tahunAjaran) ?>">
            <input type="hidden" name="jurusan" value="<?= e($jurusan) ?>">
            <div id="containerCheckboxSiswa">
                <!-- Checkbox id_riwayat[] diisi via JS -->
            </div>
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3"
                 style="border-top:1px solid #e9ecef;">
                <!-- Info Siswa -->
                <div class="text-muted small">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    Siswa yang dipilih akan diranking menggunakan SAW.
                    Peringkat 1–<span><?= $kuotaEligible ?></span> dinyatakan Eligible SNBP.
                </div>
                <!-- Tombol Hitung -->
                <button type="submit" class="btn btn-success px-4 fw-medium" id="btnProsesHitung" disabled>
                    <i class="bi bi-play-circle me-1"></i>
                    Hitung Peringkat
                    <span class="badge bg-white text-success ms-1" id="badgeDipilih">0</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Info Page -->
<div id="eligiblePageDataInfo" 
     data-tahun-ajaran="<?= e($tahunAjaran) ?>" 
     data-jurusan="<?= e($jurusan) ?>" 
     data-kuota="<?= (int)$kuotaEligible ?>"
     data-ajax-url="<?= e(url('perhitungan/eligible-get-siswa')) ?>">
</div>
<!-- Script -->
<script src="<?= asset('assets/js/perhitungan/eligible-hitung.js') ?>"></script>

<?php endif; ?>
