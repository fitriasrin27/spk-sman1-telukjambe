<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Login'); ?> - <?= e(app_config('name')); ?></title>
    <link rel="icon" type="image/png" href="<?= e(asset('assets/img/logo-sman1-nobg.png')); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= e(asset('assets/css/style.css')); ?>">
</head>
<body class="auth-page">
    <main class="container-fluid p-0 min-vh-100 bg-white">
        <div class="row g-0 min-vh-100">
            <section class="col-lg-6 d-none d-lg-block login-visual"></section>
            <section class="col-12 col-lg-6 d-flex align-items-center justify-content-center p-4">
                <!-- Card Login -->
                <div class="login-card w-100">
                    <!-- Title -->
                    <div class="text-center mb-4">
                        <img class="login-logo mb-3" src="<?= e(asset('assets/img/logo-sman1-nobg.png')); ?>" alt="Logo SMA Negeri 1 Telukjambe">
                        <h1 class="h3 fw-bold mb-4">Sistem Pendukung Keputusan<br>SMA Negeri 1 Telukjambe</h1>
                        <h2 class="login-title h3 fw-bold text-primary border-bottom pb-3">Log In</h2>
                    </div>
                    <!-- Error Message -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= e($error); ?></div>
                    <?php endif; ?>
                    <!-- Form Login -->
                    <form action="<?= e(url('authenticate')); ?>" method="post">
                        <!-- Username -->
                        <div class="mb-3 text-start">
                            <label class="form-label" for="username">Username</label>
                            <input class="form-control bg-light border-0" type="text" id="username" name="username" placeholder="Ketik Username disini..." autocomplete="username" required>
                        </div>
                        <!-- Password -->
                        <div class="mb-2 text-start">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-sm">
                                <input class="form-control bg-light border-0" type="password" id="password" name="password" placeholder="Ketik Password disini..." autocomplete="current-password" required>
                                <button class="btn btn-light border-0" type="button" id="togglePassword" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">It must be a combination of minimum 8 letters and numbers.</div>
                        </div>
                        <!-- Remember Me -->
                        <div class="form-check my-3 text-start">
                            <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <!-- Login Button -->
                        <button class="btn btn-primary w-100" type="submit">Log In</button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= e(asset('assets/js/app.js')); ?>"></script>
</body>
</html>

