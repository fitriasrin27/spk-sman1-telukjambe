<?php
[$active, $subActive] = resolveMenu();
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Dashboard'); ?> - <?= e(app_config('name')); ?></title>
    <link rel="icon" type="image/png" href="<?= e(asset('assets/img/logo-sman1-nobg.png')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= e(asset('assets/css/style.css')); ?>">
</head>
<body class="app-body">
<div class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top app-navbar">
        <div class="container-fluid px-lg-5">
            <!-- Logo SMAN 1 Telukjambe dan Nama Sekolah -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= e(url('dashboard')); ?>">
                <img src="<?= e(asset('assets/img/logo-sman1-nobg.png')); ?>" alt="Logo SMAN 1 Telukjambe" width="42" height="42">
                <div class="brand-text">
                    <strong>SMA Negeri 1 Telukjambe</strong>
                    <small>Sistem Pendukung Keputusan</small>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <!-- Nav Menu -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <!-- Home -->
                    <li class="nav-item">
                        <a class="nav-link <?= ($active === 'dashboard') ? 'active' : ''; ?>" href="<?= e(url('dashboard')); ?>">Home</a>
                    </li>

                    <?php if (in_array(current_user()['role'], ['operator', 'wakasek', 'tu', 'wali_kelas', 'bk'])): ?>
                    <!-- Data Siswa -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= ($active === 'siswa') ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">Data Siswa</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?= ($subActive === 'identitas') ? 'sub-active' : ''; ?>" href="<?= e(url('siswa')); ?>">Identitas Siswa</a></li>
                            <li><a class="dropdown-item <?= ($subActive === 'nilai') ? 'sub-active' : ''; ?>" href="<?= e(url('nilai')); ?>">Nilai</a></li>
                            <li><a class="dropdown-item <?= ($subActive === 'riwayat-kelas') ? 'sub-active' : ''; ?>" href="<?= e(url('riwayat-kelas')); ?>">Riwayat Kelas</a></li>
                            <li><a class="dropdown-item <?= ($subActive === 'mata-pelajaran') ? 'sub-active' : ''; ?>" href="<?= e(url('mata-pelajaran')); ?>">Mata Pelajaran</a></li>
                        </ul>
                    </li>
                    <!-- Data Kriteria -->
                    <?php if (!in_array(current_user()['role'], ['kepala_sekolah', 'tu', 'wakasek'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= ($active === 'kriteria') ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">Kriteria</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?= ($subActive === 'kriteria') ? 'sub-active' : ''; ?>" href="<?= e(url('kriteria')); ?>">Kriteria & Bobot</a></li>
                            <li><a class="dropdown-item <?= ($subActive === 'konversi') ? 'sub-active' : ''; ?>" href="<?= e(url('konversi')); ?>">Konversi Nilai</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!in_array(current_user()['role'], ['kepala_sekolah'])): ?>
                    <!-- Perhitungan -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= ($active === 'perhitungan') ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">Perhitungan</a>
                        <ul class="dropdown-menu">
                            <?php if (current_user()['role'] !== 'wakasek'): ?>
                            <!-- Header Proses Perhitungan -->
                            <li><h6 class="dropdown-header">Proses Perhitungan</h6></li>
                            <?php if (current_user()['role'] !== 'bk'): ?>
                            <!-- Hitung Peringkat Kelas -->
                            <li><a class="dropdown-item <?= ($subActive === 'hitung_kelas') ? 'sub-active' : ''; ?>" href="<?= e(url('perhitungan/kelas')); ?>">Peringkat Kelas</a></li>
                            <?php endif; ?>
                            <?php if (current_user()['role'] !== 'wali_kelas'): ?>
                            <!-- Hitung Peringkat Eligible -->
                            <li><a class="dropdown-item <?= ($subActive === 'hitung_eligible') ? 'sub-active' : ''; ?>" href="<?= e(url('perhitungan/eligible')); ?>">Peringkat Eligible</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <!-- Header Hasil Akhir -->
                            <li><h6 class="dropdown-header">Hasil Akhir</h6></li>
                            <?php if (current_user()['role'] !== 'bk'): ?>
                            <!-- Hasil Peringkat Kelas -->
                            <li><a class="dropdown-item <?= ($subActive === 'hasil_kelas') ? 'sub-active' : ''; ?>" href="<?= e(url('hasil/kelas')); ?>">Peringkat Kelas</a></li>
                            <?php endif; ?>
                            <?php if (current_user()['role'] !== 'wali_kelas'): ?>
                            <!-- Hasil Peringkat Eligible -->
                            <li><a class="dropdown-item <?= ($subActive === 'hasil_eligible') ? 'sub-active' : ''; ?>" href="<?= e(url('hasil/eligible')); ?>">Peringkat Eligible</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php endif; ?>

                    <!-- Laporan -->
                    <li class="nav-item">
                        <a class="nav-link <?= ($active === 'laporan') ? 'active' : ''; ?>" href="<?= e(url('laporan')); ?>">Laporan</a>
                    </li>

                    <?php if (current_user()['role'] === 'operator'): ?>
                    <!-- Kelola Akun Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= in_array($active, ['akun', 'log']) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                            Kelola Akun
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item <?= ($active === 'akun') ? 'sub-active' : ''; ?>" href="<?= e(url('akun')); ?>">Kelola Akun</a></li>
                            <li><a class="dropdown-item <?= ($active === 'log') ? 'sub-active' : ''; ?>" href="<?= e(url('log')); ?>">Log Aktivitas</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>

                </ul>

                <!-- Nav Kanan: Notif & User -->
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <div class="dropdown notif-wrapper" id="notifDropdown">
                        <a class="notif-icon" href="#" data-bs-toggle="dropdown" aria-label="Notifikasi">
                            <i class="bi bi-bell-fill"></i>
                            <?php
                            $notifList  = array_slice(array_reverse($_SESSION['notif'] ?? []), 0, 10);
                            $notifCount = count($_SESSION['notif'] ?? []);
                            ?>
                            <?php if ($notifCount > 0): ?>
                                <span class="notif-badge"><?= $notifCount > 9 ? '9+' : $notifCount ?></span>
                            <?php endif; ?>
                        </a>
                        <!-- Dropdown Notifikasi -->
                        <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0">
                            <!-- Header Notifikasi -->
                            <div class="notif-header d-flex justify-content-between align-items-center">
                                <div>
                                    <span>Notifikasi</span>
                                    <?php if ($notifCount > 0): ?>
                                        <span class="notif-count-badge"><?= $notifCount ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($notifCount > 0): ?>
                                    <!-- Tombol Hapus Semua -->
                                    <button class="btn btn-sm text-danger p-0 border-0" id="btnDeleteAllNotif" title="Bersihkan Semua">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <!-- Daftar Notifikasi -->
                            <div class="notif-list" id="notifList">
                                <?php if (!empty($notifList)): ?>
                                    <?php
                                    // Tampilkan max 10 notifikasi
                                    $totalNotif = count($_SESSION['notif']);
                                    foreach ($notifList as $i => $n):
                                        // Index asli di sesi (untuk dismiss)
                                        $sessionIndex = $totalNotif - 1 - $i;
                                        // Unix timestamp untuk format jam di JS
                                        $ts = $n['ts'] ?? strtotime($n['time']);
                                        // Tentukan ikon & warna berdasarkan pesan
                                        $isHapus  = stripos($n['message'], 'dihapus') !== false;
                                        $isImport = stripos($n['message'], 'import') !== false;
                                        $isEdit   = stripos($n['message'], 'diperbarui') !== false;
                                        if ($isHapus)       { $iconClass = 'bi-trash3-fill'; $iconColor = '#dc3545'; }
                                        elseif ($isImport)  { $iconClass = 'bi-file-earmark-arrow-up-fill'; $iconColor = '#6f42c1'; }
                                        elseif ($isEdit)    { $iconClass = 'bi-pencil-square'; $iconColor = '#fd7e14'; }
                                        else                { $iconClass = 'bi-person-plus-fill'; $iconColor = '#198754'; }
                                    ?>
                                    <!-- Notifikasi -->
                                    <div class="notif-item" data-index="<?= $sessionIndex ?>" data-ts="<?= $ts ?>">
                                        <!-- Ikon Notifikasi -->
                                        <div class="notif-item-icon" style="color: <?= $iconColor ?>">
                                            <i class="bi <?= $iconClass ?>"></i>
                                        </div>
                                        <!-- Pesan Notifikasi -->
                                        <div class="notif-item-body">
                                            <div class="notif-item-msg"><?= e($n['message']) ?></div>
                                            <!-- Waktu Notifikasi -->
                                            <div class="notif-item-footer">
                                                <span class="notif-time-ago"><?= time_elapsed($n['time']) ?></span>
                                                <span class="notif-time-exact" data-ts="<?= $ts ?>"></span>
                                            </div>
                                        </div>
                                        <!-- Tombol Tutup -->
                                        <button class="notif-dismiss" title="Tutup" data-index="<?= $sessionIndex ?>">&times;</button>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Notifikasi Kosong -->
                                    <div class="notif-empty">
                                        <i class="bi bi-bell-slash"></i>
                                        <span>Tidak ada notifikasi</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <?php $curr = current_user(); ?>
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <!-- Avatar User -->
                            <?php if (!empty($curr['foto'])): ?>
                                <img src="<?= asset('public/uploads/profile/' . $curr['foto']) ?>" class="rounded-circle border me-2 avatar-circle shadow-sm" style="width: 32px; height: 32px; object-fit: cover;">
                            <?php else: ?>
                                <!-- Avatar Default -->
                                <span class="rounded-circle bg-white text-primary border border-primary-subtle text-center me-2 avatar-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; font-size: 0.85rem; font-weight: 700;">
                                    <?= e(substr($curr['nama'] ?? 'U', 0, 1)); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- Profil User -->
                            <li>
                                <a class="dropdown-item <?= ($subActive === 'profile') ? 'sub-active' : ''; ?>" href="<?= e(url('profile')); ?>">
                                    <i class="bi bi-person-circle me-2"></i>Lihat Profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <!-- Logout -->
                            <li>
                                <a class="dropdown-item text-danger" href="<?= e(url('logout')); ?>">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-fill container-fluid px-3 px-lg-5 py-4">
        <?= $content; ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer mt-auto border-top">
        <small>&copy; 2026 SMA Negeri 1 Telukjambe. All rights reserved.</small><br>
        <small>Developed by Fitri Asri Nur Fatimah</small>
    </footer>
</div>

<?php
// Siapkan data toast: notifikasi paling baru yang perlu ditampilkan popup
// Kita gunakan flag session untuk tahu apakah ada notif baru yang belum di-toast
$toastNotif = null;
if (!empty($_SESSION['notif_new'])) {
    $toastNotif = $_SESSION['notif_new'];
    unset($_SESSION['notif_new']);
}
?>

<!-- Toast Popup Container -->
<div id="toast-container" aria-live="polite" aria-atomic="true"></div>

<?php if ($toastNotif): ?>
<script>
    window._newNotif = <?= json_encode($toastNotif) ?>;
</script>
<?php endif; ?>

<!-- Script JS -->
<script>
    // Notifikasi
    const dismissUrl     = '<?= e(url('notif/dismiss')) ?>';
    const clearAllUrl    = '<?= e(url('notif/clear-all')) ?>';
    // Riwayat Kelas
    const searchSiswaUrl = '<?= e(url('riwayat-kelas/search-siswa')) ?>'; // Reusable
    // Nilai
    const getRiwayatUrl  = '<?= e(url('nilai/get-riwayat')) ?>';
    const getMapelUrl    = '<?= e(url('nilai/get-mapel')) ?>';
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('assets/js/app.js?v=' . time())); ?>"></script>
</body>
</html>

