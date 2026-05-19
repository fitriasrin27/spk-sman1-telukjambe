<?php

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: ' . url('login'));
        exit;
    }
}

function guest_only(): void
{
    if (is_logged_in()) {
        header('Location: ' . url('dashboard'));
        exit;
    }
}

function app_config(?string $key = null): mixed
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/../Config/app.php';
    }

    return $key === null ? $config : ($config[$key] ?? null);
}

function url(string $path = ''): string
{
    $baseUrl = base_url();
    $path = trim($path, '/');

    return $path === '' ? $baseUrl : $baseUrl . '/index.php?url=' . $path;
}

function asset(string $path): string
{
    $baseUrl = base_url();
    $path = ltrim($path, '/');

    return $baseUrl . '/' . $path;
}

function base_url(): string
{
    $configuredBaseUrl = trim((string) app_config('base_url'));

    if ($configuredBaseUrl !== '') {
        return rtrim($configuredBaseUrl, '/');
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $scriptDir = $scriptDir === '/' ? '' : rtrim($scriptDir, '/');

    return $scheme . '://' . $host . $scriptDir;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
