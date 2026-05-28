<?php

namespace App\Controllers;

use App\Core\Controller;

class AkunController extends Controller
{
    public function __construct()
    {
        \App\Core\RoleAccess::check('akun');
    }

    public function index(): void
    {
        $db = \App\Core\Database::connect();
        // Otomatis tambahkan kolom jika belum ada (Self-healing)
        try {
            $checkPosisi = $db->query("SHOW COLUMNS FROM users LIKE 'posisi'")->fetch();
            if (!$checkPosisi) {
                $db->query("ALTER TABLE users ADD COLUMN posisi VARCHAR(100) AFTER nama");
            }
            $checkPlain = $db->query("SHOW COLUMNS FROM users LIKE 'password_plain'")->fetch();
            if (!$checkPlain) {
                $db->query("ALTER TABLE users ADD COLUMN password_plain VARCHAR(100) AFTER password");
            }
        } catch (\Exception $e) {
            // Abaikan jika gagal, biarkan model yang menangani pengecekan kolom
        }

        require_login();

        $search = $_GET['q'] ?? '';
        $limit  = max(1, (int)($_GET['limit'] ?? 10));
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $userModel = new \App\Models\User();
        $users = $userModel->getAll($search, $limit, $offset);
        $total = $userModel->getTotal($search);
        $totalPages = (int)ceil($total / $limit);

        $this->view('akun/index', [
            'title'      => 'Kelola Akun',
            'users'      => $users,
            'total'      => $total,
            'limit'      => $limit,
            'page'       => $page,
            'totalPages' => $totalPages,
            'search'     => $search
        ], 'layouts/app');
    }

    public function store(): void
    {
        require_login();
        $model = new \App\Models\User();
        $errors = [];

        if (empty(trim($_POST['nama'] ?? ''))) $errors[] = 'Nama tidak boleh kosong.';
        if (empty(trim($_POST['username'] ?? ''))) $errors[] = 'Username tidak boleh kosong.';
        if (empty(trim($_POST['password'] ?? ''))) $errors[] = 'Password tidak boleh kosong.';
        if (empty($_POST['role'] ?? '')) $errors[] = 'Role harus dipilih.';

        if (empty($errors) && $model->isUsernameExists(trim($_POST['username']))) {
            $errors[] = 'Username sudah digunakan.';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('akun');
        }

        $model->insert($_POST);
        log_activity("Membuat akun baru: @" . trim($_POST['username']) . " (Role: " . $_POST['role'] . ")", 'akun');
        push_notif('Akun berhasil ditambahkan.');
        $this->redirect('akun');
    }

    public function update(): void
    {
        require_login();
        $model = new \App\Models\User();
        $id = (int)($_POST['id_user'] ?? 0);
        $errors = [];

        if (empty(trim($_POST['nama'] ?? ''))) $errors[] = 'Nama tidak boleh kosong.';
        if (empty(trim($_POST['username'] ?? ''))) $errors[] = 'Username tidak boleh kosong.';
        if (empty($_POST['role'] ?? '')) $errors[] = 'Role harus dipilih.';

        if (empty($errors) && $model->isUsernameExists(trim($_POST['username']), $id)) {
            $errors[] = 'Username sudah digunakan.';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('akun');
        }

        $currentData = $model->findById($id);
        $changes = [];
        if ($currentData) {
            if (trim($currentData['nama']) !== trim($_POST['nama'] ?? '')) {
                $changes[] = "Nama '" . $currentData['nama'] . "' → '" . trim($_POST['nama']) . "'";
            }
            if (trim($currentData['username']) !== trim($_POST['username'] ?? '')) {
                $changes[] = "Username @" . $currentData['username'] . " → @" . trim($_POST['username']);
            }
            if (($currentData['role'] ?? '') !== ($_POST['role'] ?? '')) {
                $changes[] = "Role '" . $currentData['role'] . "' → '" . $_POST['role'] . "'";
            }
            if (!empty($_POST['password'])) {
                $changes[] = "Mengubah password";
            }
        }

        $model->update($id, $_POST);
        $detailStr = !empty($changes) ? " (" . implode(", ", $changes) . ")" : " (tidak ada perubahan)";
        log_activity("Memperbarui akun: @" . trim($_POST['username']) . $detailStr, 'akun');
        push_notif('Akun berhasil diperbarui.');
        $this->redirect('akun');
    }

    public function delete(): void
    {
        require_login();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('akun');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        
        if ($id > 0) {
            // Jangan biarkan hapus diri sendiri (optional, tapi baik untuk keamanan)
            if ($id === (int)($_SESSION['user']['id_user'] ?? 0)) {
                $_SESSION['error'] = 'Anda tidak dapat menghapus akun Anda sendiri.';
            } else {
                $db = \App\Core\Database::connect();
                $stmtU = $db->prepare("SELECT username FROM users WHERE id_user = :id");
                $stmtU->execute([':id' => $id]);
                $usernameToDelete = $stmtU->fetchColumn() ?: "ID {$id}";

                $model = new \App\Models\User();
                $model->delete($id);
                
                log_activity("Menghapus akun: @{$usernameToDelete}", 'akun');
                
                push_notif('Akun berhasil dihapus.');
            }
        }

        $this->redirect('akun');
    }
}