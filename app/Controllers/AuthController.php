<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function login(): void
    {
        guest_only();

        $this->view('auth/login', [
            'title' => 'Login',
            'error' => $_SESSION['error'] ?? null,
        ]);

        unset($_SESSION['error']);
    }

    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $_SESSION['error'] = 'Username dan password wajib diisi.';
            $this->redirect('login');
        }

        $userModel = new \App\Models\User();
        $user = $userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Username atau password tidak sesuai.';
            $this->redirect('login');
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id_user'  => (int) $user['id_user'],
            'nama'     => $user['nama'],
            'username' => $user['username'],
            'role'     => $user['role'],
            'posisi'   => $user['posisi'] ?? '',
            'foto'     => $user['foto'] ?? null,
        ];

        $userModel->updateLastLogin((int) $user['id_user']);

        $this->redirect('dashboard');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();

        $this->redirect('login');
    }
}
