<!-- FILTER RIWAYAT PERHITUNGAN KELAS -->
<div class="mb-4">
    <form method="GET" action="<?= e(url('')) ?>">
        <input type="hidden" name="url" value="perhitungan/kelas">
        <div class="row g-2 align-items-end">
            <!-- Pilih Tahun Ajaran -->
            <div class="col-auto">
                <select name="tahun_ajaran" class="form-select form-select-sm" style="min-width:160px;">
                    <option value="">Tahun Ajaran</option>
                    <?php foreach ($optTahunAjaran as $ta): ?>
                        <option value="<?= e($ta) ?>" <?= $tahunAjaran === $ta ? 'selected' : '' ?>><?= e($ta) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Pilih Kelas -->
            <div class="col-auto">
                <select name="kelas" class="form-select form-select-sm" style="min-width:160px;">
                    <option value="">Kelas</option>
                    <?php foreach ($optKelas as $k): ?>
                        <option value="<?= e($k) ?>" <?= $kelas === $k ? 'selected' : '' ?>><?= e($k) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Pilih Semester -->
            <div class="col-auto">
                <select name="semester" class="form-select form-select-sm" style="min-width:130px;">
                    <option value="">Semester</option>
                    <option value="ganjil" <?= $semester === 'ganjil' ? 'selected' : '' ?>>Ganjil</option>
                    <option value="genap"  <?= $semester === 'genap'  ? 'selected' : '' ?>>Genap</option>
                </select>
            </div>

            <!-- Tombol Cek Riwayat -->
            <div class="col-auto">
                <button type="submit" class="btn btn-warning btn-sm text-white px-3 fw-medium">
                    <i class="bi bi-search me-1"></i>Cek Riwayat
                </button>
            </div>
            <?php if (!in_array(current_user()['role'], ['tu', 'wakasek', 'kepala_sekolah'])): ?>
            <!-- Tombol Hitung Peringkat -->
            <div class="col-auto">
                <button type="button"
                        class="btn btn-primary btn-sm px-3 fw-medium"
                        <?= ($tahunAjaran && $kelas && $semester)
                            ? 'data-bs-toggle="modal" data-bs-target="#modalHitung"'
                            : 'disabled title="Pilih Tahun Ajaran, Kelas, dan Semester terlebih dahulu"' ?>>
                    <i class="bi bi-calculator me-1"></i>Hitung Peringkat
                </button>
            </div>
            <?php endif; ?>
            <!-- Tombol Reset -->
            <?php if ($tahunAjaran || $kelas || $semester): ?>
            <div class="col-auto">
                <a href="<?= e(url('perhitungan/kelas')) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg me-1"></i>Reset
                </a>
            </div>
            <?php endif; ?>
        </div>
    </form>
</div>
