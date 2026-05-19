<?php

$sessionPath = __DIR__ . '/../storage/sessions';

if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}

session_save_path($sessionPath);
session_start();

$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require $composerAutoload;
}

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

require __DIR__ . '/../app/Helpers/auth.php';
require __DIR__ . '/../app/Helpers/app.php';

use App\Core\Router;

$router = new Router();
$router->dispatch();
