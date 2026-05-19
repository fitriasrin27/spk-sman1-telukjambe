<!-- FILTER -->
<div class="mb-4">
    <form method="GET" action="<?= e(url('')) ?>">
        <input type="hidden" name="url" value="perhitungan/eligible">
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <!-- Dropdown Tahun Ajaran -->
                <select name="tahun_ajaran" class="form-select form-select-sm" style="min-width:160px;">
                    <option value="">Tahun Ajaran</option>
                    <?php foreach ($optTahunAjaran as $ta): ?>
                        <option value="<?= e($ta) ?>" <?= $tahunAjaran === $ta ? 'selected' : '' ?>><?= e($ta) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <!-- Dropdown Jurusan -->
                <select name="jurusan" class="form-select form-select-sm" style="min-width:160px;">
                    <option value="">Jurusan</option>
                    <option value="MIPA" <?= $jurusan === 'MIPA' ? 'selected' : '' ?>>MIPA</option>
                    <option value="IPS" <?= $jurusan === 'IPS' ? 'selected' : '' ?>>IPS</option>
                </select>
            </div>
            <div class="col-auto">
                <!-- Tombol Cek Riwayat -->
                <button type="submit" class="btn btn-warning btn-sm text-white px-3 fw-medium">
                    <i class="bi bi-search me-1"></i>Cek Riwayat
                </button>
            </div>
            <!-- Button Hitung Peringkat -->
            <?php if (!in_array(current_user()['role'], ['tu', 'wakasek', 'kepala_sekolah'])): ?>
            <div class="col-auto">
                <button type="button" class="btn btn-primary btn-sm fw-medium px-3" id="btnHitungPeringkat" <?= (!$tahunAjaran || !$jurusan) ? 'disabled' : '' ?>>
                    <i class="bi bi-calculator me-1"></i>Hitung Peringkat
                </button>
            </div>
            <?php endif; ?>
            <!-- Tombol Reset -->
            <?php if ($tahunAjaran || $jurusan): ?>
                <div class="col-auto">
                    <a href="<?= e(url('perhitungan/eligible')) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i>Reset
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>
