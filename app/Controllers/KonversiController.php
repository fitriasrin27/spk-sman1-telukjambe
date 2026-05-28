<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Konversi;
use App\Models\Kriteria;

class KonversiController extends Controller
{
    public function __construct()
    {
        \App\Core\RoleAccess::check('konversi');
    }

    public function index(): void
    {
        require_login();

        $model = new Konversi();
        $kriteriaModel = new Kriteria();

        $page = (int) ($_GET['page'] ?? 1);
        $limit = (int) ($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;
        $filterKriteria = (int) ($_GET['filter_kriteria'] ?? 0);

        $result = $model->getAll($offset, $limit, $filterKriteria);
        $totalPages = (int) ceil($result['total'] / max(1, $limit));

        $allKriteria = $kriteriaModel->getAll(0, 100)['data'];

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->view('konversi/index', [
            'title'          => 'Konversi Nilai',
            'konversi'       => $result['data'],
            'allKriteria'    => $allKriteria,
            'filterKriteria' => $filterKriteria,
            'total'          => $result['total'],
            'page'           => $page,
            'limit'          => $limit,
            'totalPages'     => $totalPages,
            'offset'         => $offset,
            'error'          => $error
        ], 'layouts/app');
    }

    public function store(): void
    {
        require_login();

        $id_kriteria = (int) ($_POST['id_kriteria'] ?? 0);
        $nilai_asli = trim($_POST['nilai_asli'] ?? '');
        $nilai_konversi = (float) ($_POST['nilai_konversi'] ?? 0);

        if ($id_kriteria === 0 || $nilai_asli === '' || $nilai_konversi <= 0) {
            $_SESSION['error'] = 'Semua field harus diisi dan nilai konversi harus lebih dari 0.';
        } else {
            $model = new Konversi();
            if ($model->checkDuplicate($id_kriteria, $nilai_asli)) {
                $_SESSION['error'] = 'Data konversi untuk kriteria dan nilai asli tersebut sudah ada.';
            } else {
                $model->insert([
                    'id_kriteria' => $id_kriteria,
                    'nilai_asli' => $nilai_asli,
                    'nilai_konversi' => $nilai_konversi
                ]);
                log_activity("Menambahkan konversi nilai: Kriteria ID {$id_kriteria} ('{$nilai_asli}' → {$nilai_konversi})", 'kriteria');
                push_notif('Data konversi berhasil ditambahkan.');
            }
        }

        $this->redirect('konversi');
    }

    public function update(): void
    {
        require_login();

        $id = (int) ($_POST['id_konversi'] ?? 0);
        $id_kriteria = (int) ($_POST['id_kriteria'] ?? 0);
        $nilai_asli = trim($_POST['nilai_asli'] ?? '');
        $nilai_konversi = (float) ($_POST['nilai_konversi'] ?? 0);

        if ($id > 0 && $id_kriteria > 0 && $nilai_asli !== '' && $nilai_konversi > 0) {
            $model = new Konversi();
            if ($model->checkDuplicate($id_kriteria, $nilai_asli, $id)) {
                $_SESSION['error'] = 'Data konversi untuk kriteria dan nilai asli tersebut sudah ada.';
            } else {
                $currentData = $model->findById($id);
                $changes = [];
                if ($currentData) {
                    if ((int)$currentData['id_kriteria'] !== $id_kriteria) {
                        $changes[] = "ID Kriteria " . $currentData['id_kriteria'] . " → " . $id_kriteria;
                    }
                    if (trim($currentData['nilai_asli']) !== $nilai_asli) {
                        $changes[] = "Nilai Asli '" . $currentData['nilai_asli'] . "' → '" . $nilai_asli . "'";
                    }
                    if ((float)$currentData['nilai_konversi'] !== $nilai_konversi) {
                        $changes[] = "Nilai Konversi " . $currentData['nilai_konversi'] . " → " . $nilai_konversi;
                    }
                }

                $model->update($id, [
                    'id_kriteria' => $id_kriteria,
                    'nilai_asli' => $nilai_asli,
                    'nilai_konversi' => $nilai_konversi
                ]);
                $detailStr = !empty($changes) ? " (" . implode(", ", $changes) . ")" : " (tidak ada perubahan)";
                log_activity("Memperbarui konversi nilai: Kriteria ID {$id_kriteria} ('{$nilai_asli}')" . $detailStr, 'kriteria');
                push_notif('Data konversi berhasil diperbarui.');
            }
        } else {
            $_SESSION['error'] = 'Data tidak valid.';
        }

        $this->redirect('konversi');
    }

    public function delete(): void
    {
        require_login();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('konversi');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $model = new Konversi();
            $konv = $model->findById($id);
            $asli = $konv ? $konv['nilai_asli'] : "ID {$id}";
            $model->delete($id);
            log_activity("Menghapus konversi nilai untuk nilai asli: '{$asli}'", 'kriteria');
            push_notif('Data konversi berhasil dihapus.');
        }

        $this->redirect('konversi');
    }
}