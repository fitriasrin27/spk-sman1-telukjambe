<?php
// Definisi Role
$roleNames = [
    'operator'        => 'Staff IT',
    'wali_kelas'      => 'Wali Kelas',
    'bk'              => 'Guru BK',
    'tu'              => 'Staff Tata Usaha',
    'wakasek'         => 'Wakasek Kurikulum',
    'kepala_sekolah'  => 'Kepala Sekolah',
    'admin'           => 'Administrator',
];
// Definisi Badge Role
$roleBadges = [
    'operator'        => ['label' => 'OP',      'style' => 'background:#cfe2ff;color:#084298;'],
    'wali_kelas'      => ['label' => 'Walas',   'style' => 'background:#d1e7dd;color:#0a3622;'],
    'bk'              => ['label' => 'BK',      'style' => 'background:#e8d5ff;color:#432874;'],
    'tu'              => ['label' => 'TU',      'style' => 'background:#ffe5d0;color:#7c3c00;'],
    'wakasek'         => ['label' => 'Wakasek', 'style' => 'background:#cff4fc;color:#055160;'],
    'kepala_sekolah'  => ['label' => 'Kepsek',  'style' => 'background:#f8d7da;color:#842029;'],
    'admin'           => ['label' => 'Admin',   'style' => 'background:#e0f2fe;color:#0369a1;'],
];

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<div class="page-header">
    <h2 class="page-title">Kelola Akun</h2>
</div>

<!-- Tombol Tambah Akun -->
<div class="mb-4">
    <button class="btn btn-primary px-4 fw-medium shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAkun" onclick="resetForm()">
        <i class="bi bi-plus-lg me-1"></i>Tambah Akun
    </button>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-4">
        <!-- Top Controls: Show Entries & Search -->
        <form method="GET" action="<?= url('akun') ?>" id="formFilterAkun">
            <input type="hidden" name="url" value="akun">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">Show</label>
                    <select name="limit" class="form-select form-select-sm d-inline-block w-auto" 
                            onchange="this.form.submit()">
                        <?php foreach ([10, 25, 50, 100] as $opt): ?>
                            <option value="<?= $opt ?>" <?= $limit === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="text-muted small mb-0">entries</label>
                </div>
                <!-- Search Input (Cari Author, Username atau Role) -->
                <div style="min-width: 250px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="q" id="searchQueryInput" class="form-control border-start-0 ps-0" 
                               placeholder="Cari author, username atau role..." autocomplete="off"
                               value="<?= e($search) ?>" autofocus>
                    </div>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 custom-table">
                <!-- Header Tabel -->
                <thead class="bg-light text-center text-dark fw-bold">
                    <tr>
                        <th width="60" class="py-3">No</th>
                        <th style="min-width: 250px;">Author</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Last Active</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <!-- Body Tabel -->
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $i => $u): ?>
                            <tr>
                                <!-- No -->
                                <td class="text-center text-dark"><?= ($page - 1) * $limit + $i + 1 ?></td>
                                <!-- Author -->
                                <td>
                                    <div class="d-flex align-items-center ps-2">
                                        <?php
                                        $initials = '';
                                        $names = explode(' ', $u['nama']);
                                        foreach ($names as $n) {
                                            $initials .= strtoupper(substr($n, 0, 1));
                                        }
                                        $initials = substr($initials, 0, 2);
                                        ?>
                                        <?php if (!empty($u['foto'])): ?>
                                        <img src="<?= asset('public/uploads/profile/' . $u['foto']) ?>"
                                             class="rounded-circle me-3 object-fit-cover"
                                             style="width:38px;height:38px;object-fit:cover;border:2px solid #e0f2fe;"
                                             alt="<?= e($u['nama']) ?>">
                                        <?php else: ?>
                                        <div class="avatar-circle me-3 fw-bold text-primary rounded-circle" style="font-size: 0.8rem; background: #e0f2fe; border: none; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                            <?= $initials ?>
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark lh-1 mb-1" style="font-size: 0.9rem;"><?= e($u['nama']) ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><?= e($u['posisi'] ?: ($roleNames[$u['role']] ?? ucwords(str_replace('_', ' ', $u['role'])))) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Username -->
                                <td class="text-center small text-muted">@<?= e($u['username']) ?></td>
                                <!-- Password -->
                                <td class="text-center text-muted small"><?= e($u['password_plain'] ?: '••••••••') ?></td>
                                <!-- Role -->
                                <td class="text-center">
                                    <?php $rb = $roleBadges[$u['role']] ?? ['label' => $u['role'], 'style' => 'background:#6c757d;color:#fff;']; ?>
                                    <span class="badge" style="<?= $rb['style'] ?>; font-weight: 600; font-size: 0.7rem; padding: 4px 8px;">
                                        <?= $rb['label'] ?>
                                    </span>
                                </td>
                                <!-- Last Active -->
                                <td class="text-center small">
                                    <?php if ($u['last_login']): ?>
                                        <?php 
                                        $diff = time() - strtotime($u['last_login']);
                                        $tsText = date('d/m/Y H:i', strtotime($u['last_login']));
                                        $elapsed = time_elapsed($u['last_login']);
                                        ?>
                                        <?php if ($diff < 300): ?>
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <span class="text-success fw-bold">Active</span>
                                                <span class="text-muted" style="font-size: 0.65rem;">- <?= $elapsed ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-dark d-inline-block"><?= $tsText ?></div>
                                            <span class="text-muted ms-1" style="font-size: 0.65rem;">- <?= $elapsed ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Aksi -->
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <!-- Tombol Edit -->
                                        <button class="btn btn-sm p-0 border-0 btn-edit-akun" title="Edit"
                                                data-bs-toggle="modal" data-bs-target="#modalEditAkun"
                                                data-id="<?= $u['id_user'] ?>"
                                                data-nama="<?= e($u['nama']) ?>"
                                                data-posisi="<?= e($u['posisi'] ?? '') ?>"
                                                data-username="<?= e($u['username']) ?>"
                                                data-role="<?= e($u['role']) ?>"
                                                data-password="<?= e($u['password_plain'] ?? '') ?>"
                                                onclick="populateEditModal(this)">
                                            <i class="bi bi-pencil-square fs-5" style="color:#f59e0b;"></i>
                                        </button>
                                        <span class="aksi-separator"></span>
                                        <!-- Tombol Hapus -->
                                        <button class="btn btn-sm p-0 border-0 btn-delete" title="Hapus"
                                                onclick="confirmDelete(<?= $u['id_user'] ?>, '<?= e($u['nama']) ?>')"
                                                <?= $u['id_user'] == ($_SESSION['user']['id_user'] ?? 0) ? 'disabled' : '' ?>>
                                            <i class="bi bi-trash3 fs-5" style="color:#ef4444;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada data akun.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Show -->
        <div class="mt-3 text-muted small">
            Show <?= ($page - 1) * $limit + 1 ?> to <?= min($page * $limit, $total) ?> of <?= $total ?> entries
        </div>

        <!-- Pagination Nav -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-3">
                <ul class="pagination pagination-sm justify-content-end mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= url('akun') ?>&q=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $page - 1 ?>">Previous</a>
                    </li>
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= url('akun') ?>&q=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $p ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= url('akun') ?>&q=<?= urlencode($search) ?>&limit=<?= $limit ?>&page=<?= $page + 1 ?>">Next</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/modal-tambah.php'; ?>
<?php include __DIR__ . '/modal-edit.php'; ?>
<?php include __DIR__ . '/modal-hapus.php'; ?>



<link rel="stylesheet" href="<?= e(asset('assets/css/pages/akun.css')); ?>">
<script>
    /* URL dan referensi yang dibutuhkan oleh akun.js eksternal */
    const akunStoreUrl  = '<?= e(url('akun/store')) ?>';
    const akunDeleteUrl = '<?= e(url('akun/delete')) ?>';
</script>
<script src="<?= e(asset('assets/js/pages/akun.js')); ?>"></script>

