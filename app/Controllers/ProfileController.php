<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\RoleAccess;

class ProfileController extends Controller
{
    public function __construct()
    {
        RoleAccess::check('profile');
    }

    public function index(): void
    {
        require_login();
        $user = current_user();

        // Ambil password_plain dari DB untuk ditampilkan di form (kebutuhan internal)
        $db = Database::connect();
        $stmt = $db->prepare("SELECT password_plain, posisi FROM users WHERE id_user = ?");
        $stmt->execute([$user['id_user']]);
        $extra = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($extra) {
            $user['password_plain'] = $extra['password_plain'] ?? '';
            $user['posisi']         = $extra['posisi'] ?? ($user['posisi'] ?? '');
        }

        $this->view('profile/index', [
            'title' => 'Profil Saya',
            'user'  => $user
        ], 'layouts/app');
    }

    /**
     * Update data profil (Nama, Username, NIP)
     */
    public function update(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('profile');
            return;
        }

        $idUser   = (int)$_POST['id_user'];
        $nama     = trim($_POST['nama']);
        $username = trim($_POST['username']);
        $posisi   = trim($_POST['posisi']);

        if (!$nama || !$username) {
            $_SESSION['error'] = 'Nama dan Username wajib diisi.';
            $this->redirect('profile');
            return;
        }

        $db = Database::connect();
        try {
            $stmt = $db->prepare("UPDATE users SET nama = :nama, username = :username, posisi = :posisi WHERE id_user = :id");
            $stmt->execute([
                ':nama'     => $nama,
                ':username' => $username,
                ':posisi'   => $posisi,
                ':id'       => $idUser
            ]);

            // Update session agar data terbaru muncul
            $_SESSION['user']['nama'] = $nama;
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['posisi'] = $posisi;

            push_notif('Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal memperbarui profil: ' . $e->getMessage();
        }

        $this->redirect('profile');
    }

    /**
     * Update Foto Profil
     */
    public function updateFoto(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['foto'])) {
            $this->redirect('profile');
            return;
        }

        $file = $_FILES['foto'];
        $idUser = (int)$_POST['id_user'];

        // Validasi Upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Gagal upload file.';
            $this->redirect('profile');
            return;
        }

        // Validasi Tipe File
        $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file['type'], $allowed)) {
            $_SESSION['error'] = 'Format file harus JPG atau PNG.';
            $this->redirect('profile');
            return;
        }

        // Validasi Ukuran (Max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = 'Ukuran file maksimal 5MB.';
            $this->redirect('profile');
            return;
        }

        $db = Database::connect();
        $targetDir = 'public/uploads/profile/';
        
        // Buat folder jika belum ada
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Nama file unik
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'profile_' . $idUser . '_' . time() . '.' . $ext;
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            try {
                // Ambil foto lama untuk dihapus
                $oldData = $db->prepare("SELECT foto FROM users WHERE id_user = ?");
                $oldData->execute([$idUser]);
                $oldFoto = $oldData->fetchColumn();

                // Update database
                $stmt = $db->prepare("UPDATE users SET foto = ? WHERE id_user = ?");
                $stmt->execute([$fileName, $idUser]);

                // Hapus file fisik lama jika ada
                if ($oldFoto && file_exists($targetDir . $oldFoto)) {
                    unlink($targetDir . $oldFoto);
                }

                $_SESSION['user']['foto'] = $fileName;
                push_notif('Foto profil berhasil diperbarui.');
            } catch (\Exception $e) {
                $_SESSION['error'] = 'Gagal update database: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Gagal memindahkan file.';
        }

        $this->redirect('profile');
    }

    /**
     * Ganti Password
     */
    public function changePassword(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('profile');
            return;
        }

        $idUser      = (int)$_POST['id_user'];
        $oldPass     = $_POST['old_password'];
        $newPass     = $_POST['new_password'];
        $confirmPass = $_POST['confirm_password'];

        if ($newPass !== $confirmPass) {
            $_SESSION['error'] = 'Konfirmasi password baru tidak cocok.';
            $this->redirect('profile');
            return;
        }

        $db = Database::connect();
        try {
            // Verifikasi Password Lama
            $stmt = $db->prepare("SELECT password FROM users WHERE id_user = ?");
            $stmt->execute([$idUser]);
            $hashed = $stmt->fetchColumn();

            if (!password_verify($oldPass, $hashed)) {
                $_SESSION['error'] = 'Password lama salah.';
                $this->redirect('profile');
                return;
            }

            // Update Password Baru
            $newHashed = password_hash($newPass, PASSWORD_DEFAULT);
            $update = $db->prepare("UPDATE users SET password = ? WHERE id_user = ?");
            $update->execute([$newHashed, $idUser]);

            push_notif('Password berhasil diganti.');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal mengganti password: ' . $e->getMessage();
        }

        $this->redirect('profile');
    }
}