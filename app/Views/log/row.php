<?php
/**
 * Partial: Baris tabel Log Aktivitas
 * Di-include di dalam foreach loop pada index.php
 *
 * Variabel yang tersedia dari scope induk (via include):
 * @var array  $l          Data satu baris log dari DB
 * @var int    $i          Index iterasi dalam foreach (0-based)
 * @var array  $roleBadges Map role => ['label', 'style']
 * @var int    $page       Halaman aktif saat ini
 * @var int    $limit      Jumlah data per halaman
 */

$num      = ($page - 1) * $limit + $i + 1;
$userRole = $l['user_role'] ?? '';
$rb       = $roleBadges[$userRole] ?? null;

// -------------------------------------------------------
// Deteksi Browser dari User-Agent
// -------------------------------------------------------
$ua      = $l['user_agent'] ?? '';
$browser = 'Unknown';
if      (stripos($ua, 'firefox') !== false)                                                             $browser = 'Firefox';
elseif  (stripos($ua, 'SamsungBrowser') !== false)                                                      $browser = 'Samsung';
elseif  (stripos($ua, 'UCBrowser') !== false)                                                           $browser = 'UC Browser';
elseif  (stripos($ua, 'opr') !== false || stripos($ua, 'Opera Mini') !== false || stripos($ua, 'opera') !== false) $browser = 'Opera';
elseif  (stripos($ua, 'Edg/') !== false || stripos($ua, 'Edge') !== false)                             $browser = 'Edge';
elseif  (stripos($ua, 'MSIE') !== false || stripos($ua, 'Trident/') !== false)                         $browser = 'IE';
elseif  (stripos($ua, 'chrome') !== false)                                                              $browser = 'Chrome';
elseif  (stripos($ua, 'safari') !== false)                                                              $browser = 'Safari';

// -------------------------------------------------------
// Warna brand asli + logo nyata browser (outline, vivid)
// Logo dari jsDelivr CDN (alrra/browser-logos)
// Fallback ke Bootstrap Icon jika CDN gagal load
// -------------------------------------------------------
$cdnBase       = 'https://cdn.jsdelivr.net/gh/alrra/browser-logos@main/src';
$browserStyles = [
    'Chrome'     => ['logo' => "$cdnBase/chrome/chrome_24x24.png",                                  'icon' => 'bi-google',           'bg' => '#eef3fd', 'color' => '#4285F4', 'border' => '#4285F4'],
    'Edge'       => ['logo' => "$cdnBase/edge/edge_24x24.png",                                      'icon' => 'bi-browser-edge',     'bg' => '#e3f3fb', 'color' => '#0078D4', 'border' => '#0078D4'],
    'Firefox'    => ['logo' => "$cdnBase/firefox/firefox_24x24.png",                                'icon' => 'bi-browser-firefox',  'bg' => '#fff4ef', 'color' => '#FF7139', 'border' => '#FF7139'],
    'Safari'     => ['logo' => "$cdnBase/safari/safari_24x24.png",                                  'icon' => 'bi-compass',          'bg' => '#e5f0ff', 'color' => '#006CFF', 'border' => '#006CFF'],
    'Opera'      => ['logo' => "$cdnBase/opera/opera_24x24.png",                                    'icon' => 'bi-globe2',           'bg' => '#ffebec', 'color' => '#FF1B2D', 'border' => '#FF1B2D'],
    'Samsung'    => ['logo' => "$cdnBase/samsung-internet/samsung-internet_24x24.png",              'icon' => 'bi-phone',            'bg' => '#e8eaf6', 'color' => '#1428A0', 'border' => '#1428A0'],
    'UC Browser' => ['logo' => "$cdnBase/uc/uc_24x24.png",                                         'icon' => 'bi-lightning-charge', 'bg' => '#fff3e0', 'color' => '#FF6900', 'border' => '#FF6900'],
    'IE'         => ['logo' => "$cdnBase/internet-explorer_9-11/internet-explorer_9-11_24x24.png",  'icon' => 'bi-window',           'bg' => '#e5f7fc', 'color' => '#1EBBEE', 'border' => '#1EBBEE'],
    'Unknown'    => ['logo' => '',                                                                   'icon' => 'bi-globe',            'bg' => '#f1f3f5', 'color' => '#6c757d', 'border' => '#adb5bd'],
];
$bs            = $browserStyles[$browser] ?? $browserStyles['Unknown'];
$browserBg     = $bs['bg'];
$browserColor  = $bs['color'];
$browserBorder = $bs['border'];
$browserLogo   = $bs['logo'];
$browserIcon   = $bs['icon'];

// -------------------------------------------------------
// Versi LENGKAP dari UA — Firefox/Samsung/Opera/dll tampil
// versi asli berbeda. Chrome & Edge sama-sama .0.0.0
// karena UA Reduction policy (Google & Microsoft), wajar.
// -------------------------------------------------------
$browserVersion = '';
switch ($browser) {
    case 'Chrome':     preg_match('/Chrome\/([\.\d]+)/',           $ua, $m); $browserVersion = $m[1] ?? ''; break;
    case 'Edge':       preg_match('/Edg\/([\.\d]+)/',              $ua, $m); $browserVersion = $m[1] ?? ''; break;
    case 'Firefox':    preg_match('/Firefox\/([\.\d]+)/',          $ua, $m); $browserVersion = $m[1] ?? ''; break;
    case 'Safari':     preg_match('/Version\/([\.\d]+)/',          $ua, $m); $browserVersion = $m[1] ?? ''; break;
    case 'Opera':      preg_match('/(?:OPR|Version)\/([\.\d]+)/',  $ua, $m); $browserVersion = $m[1] ?? ''; break;
    case 'Samsung':    preg_match('/SamsungBrowser\/([\.\d]+)/',   $ua, $m); $browserVersion = $m[1] ?? ''; break;
    case 'UC Browser': preg_match('/UCBrowser\/([\.\d]+)/',        $ua, $m); $browserVersion = $m[1] ?? ''; break;
    case 'IE':         preg_match('/(?:MSIE |rv:)([\.\d]+)/',      $ua, $m); $browserVersion = $m[1] ?? ''; break;
}
$browserTooltip = $browser . ($browserVersion ? ' ' . $browserVersion : '');

// Inisial avatar
$initials = '';
$names    = explode(' ', $l['nama'] ?: ($l['username_fallback'] ?: 'U'));
foreach ($names as $n) {
    $initials .= strtoupper(substr($n, 0, 1));
}
$initials = substr($initials, 0, 2);

// Badge modul
$modClass = 'bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle';
$modLabel = strtoupper($l['modul']);
switch ($l['modul']) {
    case 'auth':        $modClass = 'bg-danger bg-opacity-10 text-danger border border-danger-subtle';   break;
    case 'siswa':       $modClass = 'bg-success bg-opacity-10 text-success border border-success-subtle'; break;
    case 'nilai':       $modClass = 'bg-warning bg-opacity-10 text-warning border border-warning-subtle'; break;
    case 'perhitungan': $modClass = 'bg-info bg-opacity-10 text-info border border-info-subtle';          break;
    case 'kriteria':    $modClass = 'bg-primary bg-opacity-10 text-primary border border-primary-subtle'; break;
    case 'laporan':     $modClass = 'bg-dark bg-opacity-10 text-dark border border-dark-subtle';          break;
    case 'akun':        $modClass = 'bg-purple-subtle text-purple border border-purple-subtle';           break;
}

$displayName = $l['nama'] ?: ($l['username_fallback'] ? '@' . $l['username_fallback'] : 'Tamu/Sistem');
?>
<tr class="log-row" data-id="<?= $l['id_log']; ?>">
    <!-- No -->
    <td class="text-center align-middle text-dark num-cell"><?= $num ?></td>

    <!-- Waktu -->
    <td class="text-center align-middle time-cell">
        <div class="fw-bold text-dark mb-0.5" style="font-size: 0.8rem;">
            <?= time_elapsed($l['created_at']); ?>
        </div>
        <small class="text-muted d-block" style="font-size: 0.72rem;">
            <?= date('d/m/Y H:i:s', strtotime($l['created_at'])); ?>
        </small>
    </td>

    <!-- Pengguna -->
    <td class="align-middle">
        <div class="d-flex align-items-center ps-1">
            <?php if (!empty($l['foto'])): ?>
                <img src="<?= asset('public/uploads/profile/' . $l['foto']) ?>"
                     class="rounded-circle me-2-5 object-fit-cover border"
                     style="width: 32px; height: 32px;">
            <?php else: ?>
                <div class="avatar-circle me-2-5 fw-bold text-primary rounded-circle"
                     style="font-size: 0.75rem; background: #e0f2fe; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                    <?= $initials ?>
                </div>
            <?php endif; ?>

            <div style="min-width:0;">
                <div class="fw-bold text-dark lh-1 mb-1"
                     style="font-size: 0.78rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 145px;"
                     data-bs-toggle="tooltip" title="<?= e($displayName) ?>">
                    <?= e($displayName) ?>
                </div>
                <?php if ($rb): ?>
                    <span class="badge" style="<?= $rb['style'] ?>; font-size: 0.62rem; padding: 2px 6px; font-weight: 600;">
                        <?= $rb['label'] ?>
                    </span>
                <?php else: ?>
                    <span class="badge bg-secondary text-white" style="font-size: 0.62rem; padding: 2px 6px; font-weight: 600;">System</span>
                <?php endif; ?>
            </div>
        </div>
    </td>

    <!-- Modul -->
    <td class="text-center align-middle">
        <span class="badge rounded-pill <?= $modClass ?>" style="font-size:0.68rem; font-weight:600; padding: 4px 8px;">
            <?= $modLabel ?>
        </span>
    </td>

    <!-- Aktivitas -->
    <td class="text-dark small lh-sm align-middle"><?= $l['aktivitas']; ?></td>

    <!-- IP Address -->
    <td class="text-center align-middle small text-muted">
        <span class="badge bg-light text-secondary border border-light-subtle px-2 py-1">
            <i class="bi bi-laptop me-1"></i><?= e($l['ip_address']); ?>
        </span>
    </td>

    <!-- Browser -->
    <td class="text-center align-middle small">
        <span data-bs-toggle="tooltip" title="<?= e($browserTooltip) ?>"
              class="badge px-2 py-1 d-inline-flex align-items-center gap-1"
              style="background:<?= $browserBg ?>; color:<?= $browserColor ?>; border:1px solid <?= $browserBorder ?>; font-weight:600; line-height:1.3;">
            <?php if ($browserLogo): ?>
                <img src="<?= e($browserLogo) ?>" width="13" height="13"
                     style="vertical-align:middle; flex-shrink:0;"
                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none');">
                <i class="bi <?= $browserIcon ?> d-none" style="font-size:0.8rem;"></i>
            <?php else: ?>
                <i class="bi <?= $browserIcon ?>" style="font-size:0.8rem;"></i>
            <?php endif; ?>
            <?= $browser ?>
        </span>
    </td>
</tr>
