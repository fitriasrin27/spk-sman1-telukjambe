<?php

namespace App\Controllers;

use App\Core\Controller;

class KriteriaController extends Controller
{
    public function __construct()
    {
        \App\Core\RoleAccess::check('kriteria');
    }

    public function index(): void
    {
        require_login();

        $model = new \App\Models\Kriteria();
        
        $limit = max(1, (int) ($_GET['limit'] ?? 10));
        $page  = max(1, (int) ($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $result = $model->getAll($offset, $limit);
        $totalBobot = $model->getTotalBobot();
        $nextKode = $model->getNextKode();
        $totalPages = (int) ceil($result['total'] / max(1, $limit));

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->view('kriteria/index', [
            'title'      => 'Kriteria & Bobot',
            'kriteria'   => $result['data'],
            'totalBobot' => $totalBobot,
            'nextKode'   => $nextKode,
            'total'      => $result['total'],
            'page'       => $page,
            'limit'      => $limit,
            'totalPages' => $totalPages,
            'offset'     => $offset,
            'error'      => $error
        ], 'layouts/app');
    }

    public function store(): void
    {
        require_login();
        $model = new \App\Models\Kriteria();
        
        $kode = trim($_POST['kode_kriteria'] ?? '');
        $nama = trim($_POST['nama_kriteria'] ?? '');
        $atribut = trim($_POST['atribut'] ?? '');
        $bobot = (float) ($_POST['bobot'] ?? 0);

        if ($kode === '' || $nama === '' || $atribut === '' || $bobot <= 0) {
            $_SESSION['error'] = 'Semua field harus diisi dan bobot harus lebih dari 0.';
        } elseif ($model->findByKode($kode)) {
            $_SESSION['error'] = 'Gagal: Kode Kriteria "' . e($kode) . '" sudah digunakan.';
        } else {
            $currentTotal = $model->getTotalBobot();
            $sisa = 1.00 - $currentTotal;
            
            // Allow a small epsilon for floating point comparison issues, though usually 2 decimals is fine.
            if (round($currentTotal + $bobot, 2) > 1.00) {
                $_SESSION['error'] = 'Gagal: Total bobot melebihi batas 1.00. Sisa kuota bobot Anda adalah ' . number_format($sisa, 2) . '.';
            } else {
                $model->insert([
                    'kode_kriteria' => $kode,
                    'nama_kriteria' => $nama,
                    'atribut' => $atribut,
                    'bobot' => $bobot
                ]);
                log_activity("Menambahkan kriteria baru: {$nama} ({$kode}, Bobot: {$bobot})", 'kriteria');
                push_notif('Kriteria berhasil ditambahkan.');
            }
        }

        $this->redirect('kriteria');
    }

    public function update(): void
    {
        require_login();
        $model = new \App\Models\Kriteria();

        $id = (int) ($_POST['id_kriteria'] ?? 0);
        $kode = trim($_POST['kode_kriteria'] ?? '');
        $nama = trim($_POST['nama_kriteria'] ?? '');
        $atribut = trim($_POST['atribut'] ?? '');
        $bobot = (float) ($_POST['bobot'] ?? 0);

        if ($id > 0 && $kode !== '' && $nama !== '' && $atribut !== '' && $bobot > 0) {
            $existing = $model->findByKode($kode);
            if ($existing && $existing['id_kriteria'] != $id) {
                $_SESSION['error'] = 'Gagal: Kode Kriteria "' . e($kode) . '" sudah digunakan oleh kriteria lain.';
                $this->redirect('kriteria');
                return;
            }

            $currentData = $model->findById($id);
            if (!$currentData) {
                $_SESSION['error'] = 'Data tidak ditemukan.';
                $this->redirect('kriteria');
                return;
            }

            $oldBobot = (float) $currentData['bobot'];
            $currentTotal = $model->getTotalBobot();
            $totalWithoutCurrent = $currentTotal - $oldBobot;
            $sisa = 1.00 - $totalWithoutCurrent;

            $changes = [];
            if ($currentData) {
                if (trim($currentData['kode_kriteria']) !== $kode) {
                    $changes[] = "Kode '" . $currentData['kode_kriteria'] . "' → '" . $kode . "'";
                }
                if (trim($currentData['nama_kriteria']) !== $nama) {
                    $changes[] = "Nama '" . $currentData['nama_kriteria'] . "' → '" . $nama . "'";
                }
                if (trim($currentData['atribut']) !== $atribut) {
                    $changes[] = "Atribut '" . $currentData['atribut'] . "' → '" . $atribut . "'";
                }
                if ((float)$currentData['bobot'] !== $bobot) {
                    $changes[] = "Bobot '" . $currentData['bobot'] . "' → '" . $bobot . "'";
                }
            }

            if (round($totalWithoutCurrent + $bobot, 2) > 1.00) {
                $_SESSION['error'] = 'Gagal: Total bobot melebihi batas 1.00. Sisa kuota bobot (termasuk kriteria ini) adalah ' . number_format($sisa, 2) . '.';
            } else {
                $model->update($id, [
                    'kode_kriteria' => $kode,
                    'nama_kriteria' => $nama,
                    'atribut' => $atribut,
                    'bobot' => $bobot
                ]);
                $detailStr = !empty($changes) ? " (" . implode(", ", $changes) . ")" : " (tidak ada perubahan)";
                log_activity("Memperbarui kriteria: {$nama} ({$kode})" . $detailStr, 'kriteria');
                push_notif('Kriteria berhasil diperbarui.');
            }
        } else {
            $_SESSION['error'] = 'Data tidak valid.';
        }

        $this->redirect('kriteria');
    }

    public function delete(): void
    {
        require_login();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('kriteria');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $model = new \App\Models\Kriteria();
            $k = $model->findById($id);
            $nama = $k ? $k['nama_kriteria'] : "ID {$id}";
            $model->delete($id);
            log_activity("Menghapus kriteria: {$nama}", 'kriteria');
            push_notif('Kriteria berhasil dihapus.');
        }

        $this->redirect('kriteria');
    }
}