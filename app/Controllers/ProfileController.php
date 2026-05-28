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

        $idUser   = current_user()['id_user'];
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
            // Ambil data profil lama untuk dibandingkan
            $stmtOld = $db->prepare("SELECT nama, username, posisi FROM users WHERE id_user = ?");
            $stmtOld->execute([$idUser]);
            $oldData = $stmtOld->fetch(\PDO::FETCH_ASSOC);

            $changes = [];
            if ($oldData) {
                if (trim($oldData['nama']) !== $nama) {
                    $changes[] = "Nama '" . $oldData['nama'] . "' → '" . $nama . "'";
                }
                if (trim($oldData['username']) !== $username) {
                    $changes[] = "Username @" . $oldData['username'] . " → @" . $username;
                }
                if (trim($oldData['posisi']) !== $posisi) {
                    $changes[] = "Posisi '" . $oldData['posisi'] . "' → '" . $posisi . "'";
                }
            }

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

            $detailStr = !empty($changes) ? " (" . implode(", ", $changes) . ")" : " (tidak ada perubahan)";
            log_activity("Memperbarui profil diri: @" . $username . $detailStr, 'akun');

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('profile');
            return;
        }

        $idUser = current_user()['id_user'];
        $croppedData = $_POST['cropped_image'] ?? '';

        if (empty($croppedData)) {
            $_SESSION['error'] = 'Tidak ada gambar yang dikirim.';
            $this->redirect('profile');
            return;
        }

        // Dekode Base64
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $croppedData, $type)) {
                $data = substr($croppedData, strpos($croppedData, ',') + 1);
                $type = strtolower($type[1]); // jpg, jpeg, png, dll

                if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                    $_SESSION['error'] = 'Format file harus JPG atau PNG.';
                    $this->redirect('profile');
                    return;
                }

                $data = base64_decode($data);
                if ($data === false) {
                    $_SESSION['error'] = 'Gagal memproses data gambar.';
                    $this->redirect('profile');
                    return;
                }
            } else {
                $_SESSION['error'] = 'Format data gambar tidak valid.';
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
            $fileName = 'profile_' . $idUser . '_' . time() . '.' . $type;
            $targetFile = $targetDir . $fileName;

            if (file_put_contents($targetFile, $data) !== false) {
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
                log_activity("Memperbarui foto profil diri", 'akun');
                push_notif('Foto profil berhasil diperbarui.');
            } else {
                $_SESSION['error'] = 'Gagal menyimpan berkas foto.';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal memperbarui foto profil: ' . $e->getMessage();
        }

        $this->redirect('profile');
    }

    /**
     * Hapus Foto Profil
     */
    public function deleteFoto(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('profile');
            return;
        }

        $idUser = current_user()['id_user'];
        $db = Database::connect();
        $targetDir = 'public/uploads/profile/';

        try {
            // Ambil foto lama untuk dihapus
            $stmt = $db->prepare("SELECT foto FROM users WHERE id_user = ?");
            $stmt->execute([$idUser]);
            $foto = $stmt->fetchColumn();

            if ($foto) {
                // Hapus berkas fisik
                if (file_exists($targetDir . $foto)) {
                    unlink($targetDir . $foto);
                }

                // Update db
                $update = $db->prepare("UPDATE users SET foto = NULL WHERE id_user = ?");
                $update->execute([$idUser]);

                // Reset session
                $_SESSION['user']['foto'] = null;

                log_activity("Menghapus foto profil diri", 'akun');
                push_notif('Foto profil berhasil dihapus.');
            } else {
                $_SESSION['error'] = 'Anda tidak memiliki foto profil untuk dihapus.';
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal menghapus foto profil: ' . $e->getMessage();
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

        $idUser      = current_user()['id_user'];
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

            // Update Password Baru & Password Plain
            $newHashed = password_hash($newPass, PASSWORD_DEFAULT);
            
            // Cek apakah password_plain ada
            $colsPlain = $db->query("SHOW COLUMNS FROM users LIKE 'password_plain'")->fetch();
            if ($colsPlain) {
                $update = $db->prepare("UPDATE users SET password = ?, password_plain = ? WHERE id_user = ?");
                $update->execute([$newHashed, $newPass, $idUser]);
            } else {
                $update = $db->prepare("UPDATE users SET password = ? WHERE id_user = ?");
                $update->execute([$newHashed, $idUser]);
            }

            log_activity("Mengubah kata sandi profil diri", 'akun');
            push_notif('Password berhasil diganti.');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Gagal mengganti password: ' . $e->getMessage();
        }

        $this->redirect('profile');
    }
}