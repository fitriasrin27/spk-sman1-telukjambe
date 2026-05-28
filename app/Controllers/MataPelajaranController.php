<?php

namespace App\Controllers;

use App\Core\Controller;

class MataPelajaranController extends Controller
{
    public function __construct()
    {
        \App\Core\RoleAccess::check('mata-pelajaran');
    }

    public function index(): void
    {
        require_login();

        $tingkat = $_GET['tingkat'] ?? '';
        $jurusan = $_GET['jurusan'] ?? '';
        $search  = $_GET['q'] ?? '';
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $limit   = max(10, (int) ($_GET['limit'] ?? 10));
        $offset  = ($page - 1) * $limit;

        $model = new \App\Models\MataPelajaran();

        $mapelList    = $model->getAll($limit, $offset, $tingkat, $jurusan, $search);
        $total        = $model->countAll($tingkat, $jurusan, $search);
        $totalPages   = $total > 0 ? ceil($total / $limit) : 1;
        $daftarJurusan= $model->getDaftarJurusan();
        $error        = $_SESSION['error'] ?? null;

        unset($_SESSION['error']);

        $this->view('mata-pelajaran/index', [
            'title'         => 'Mata Pelajaran',
            'tingkat'       => $tingkat,
            'jurusan'       => $jurusan,
            'search'        => $search,
            'limit'         => $limit,
            'page'          => $page,
            'total'         => $total,
            'totalPages'    => $totalPages,
            'offset'        => $offset,
            'mapelList'     => $mapelList,
            'daftarJurusan' => $daftarJurusan,
            'error'         => $error,
        ], 'layouts/app');
    }

    public function store(): void
    {
        require_login();

        $tingkat = $_POST['tingkat'] ?? '';
        $jurusan = trim($_POST['jurusan'] ?? '');
        $mapel   = $_POST['mapel'] ?? []; // Array dari baris dinamis [ ['kode' => '..', 'nama' => '..'], ... ]

        $errors = [];

        if ($tingkat === '') $errors[] = 'Tingkat belum dipilih.';
        if ($jurusan === '') $errors[] = 'Jurusan tidak boleh kosong.';
        if (empty($mapel) || !is_array($mapel)) {
            $errors[] = 'Minimal satu mata pelajaran harus diisi.';
        }

        if (empty($errors)) {
            $model = new \App\Models\MataPelajaran();
            $inserted = 0;
            $skipped = 0;
            $insertedList = [];

            foreach ($mapel as $m) {
                $kode = trim($m['kode'] ?? '');
                $nama = trim($m['nama'] ?? '');

                if ($kode === '' || $nama === '') continue;

                if ($model->isDuplicate($kode, $tingkat, $jurusan)) {
                    $skipped++;
                    continue;
                }

                $model->insert([
                    'kode_mapel' => $kode,
                    'nama_mapel' => $nama,
                    'tingkat'    => $tingkat,
                    'jurusan'    => $jurusan,
                ]);
                $inserted++;
                $insertedList[] = "{$nama} ({$kode})";
            }

            if ($inserted > 0) {
                $msg = "$inserted mata pelajaran berhasil ditambahkan ke Tingkat $tingkat $jurusan.";
                if ($skipped > 0) $msg .= " ($skipped dilewati karena duplikat)";
                push_notif($msg);

                $insertedDetail = implode(', ', $insertedList);
                log_activity("Menambahkan {$inserted} mata pelajaran baru ke Tingkat {$tingkat} {$jurusan}: {$insertedDetail}", 'siswa');
            } elseif ($skipped > 0) {
                $_SESSION['error'] = 'Semua mapel yang dimasukkan sudah ada (duplikat).';
            } else {
                $_SESSION['error'] = 'Form mapel kosong atau tidak valid.';
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errors);
        }

        $this->redirect('mata-pelajaran');
    }

    public function update(): void
    {
        require_login();

        $id      = (int) ($_POST['id_mapel'] ?? 0);
        $tingkat = $_POST['tingkat'] ?? '';
        $jurusan = trim($_POST['jurusan'] ?? '');
        $kode    = trim($_POST['kode_mapel'] ?? '');
        $nama    = trim($_POST['nama_mapel'] ?? '');

        $errors = [];

        if ($id <= 0)       $errors[] = 'Data tidak valid.';
        if ($tingkat === '') $errors[] = 'Tingkat harus dipilih.';
        if ($jurusan === '') $errors[] = 'Jurusan tidak boleh kosong.';
        if ($kode === '')    $errors[] = 'Kode mapel tidak boleh kosong.';
        if ($nama === '')    $errors[] = 'Nama mapel tidak boleh kosong.';

        $model = new \App\Models\MataPelajaran();

        if (empty($errors) && $model->isDuplicate($kode, $tingkat, $jurusan, $id)) {
            $errors[] = 'Mata pelajaran ini sudah ada di tingkat dan jurusan yang sama.';
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
        } else {
            $old = $model->findById($id);
            $changes = [];
            if ($old) {
                if (trim($old['kode_mapel']) !== $kode) {
                    $changes[] = "Kode Mapel '" . $old['kode_mapel'] . "' → '" . $kode . "'";
                }
                if (trim($old['nama_mapel']) !== $nama) {
                    $changes[] = "Nama Mapel '" . $old['nama_mapel'] . "' → '" . $nama . "'";
                }
                if (trim($old['tingkat']) !== $tingkat) {
                    $changes[] = "Tingkat '" . $old['tingkat'] . "' → '" . $tingkat . "'";
                }
                if (trim($old['jurusan']) !== $jurusan) {
                    $changes[] = "Jurusan '" . $old['jurusan'] . "' → '" . $jurusan . "'";
                }
            }

            $model->update([
                'id_mapel'   => $id,
                'kode_mapel' => $kode,
                'nama_mapel' => $nama,
                'tingkat'    => $tingkat,
                'jurusan'    => $jurusan,
            ]);

            $detailStr = !empty($changes) ? " (" . implode(", ", $changes) . ")" : " (tidak ada perubahan)";
            $mapelName = $old ? $old['nama_mapel'] : $nama;
            $mapelCode = $old ? $old['kode_mapel'] : $kode;
            log_activity("Memperbarui mata pelajaran: " . $mapelName . " (" . $mapelCode . ")" . $detailStr, 'siswa');

            push_notif('Mata pelajaran berhasil diperbarui.');
        }

        $this->redirect('mata-pelajaran');
    }

    public function delete(): void
    {
        require_login();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('mata-pelajaran');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $model = new \App\Models\MataPelajaran();
            $old = $model->findById($id);
            $model->delete($id);

            if ($old) {
                log_activity("Menghapus mata pelajaran: " . $old['nama_mapel'] . " (" . $old['kode_mapel'] . ")", 'siswa');
            } else {
                log_activity("Menghapus mata pelajaran ID {$id}", 'siswa');
            }

            push_notif('Mata pelajaran berhasil dihapus.');
        }

        $this->redirect('mata-pelajaran');
    }

    public function deleteBatch(): void
    {
        require_login();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('mata-pelajaran');
            return;
        }

        $ids = $_POST['ids'] ?? [];
        if (empty($ids)) {
            $_SESSION['error'] = 'Tidak ada data yang dipilih.';
            $this->redirect('mata-pelajaran');
            return;
        }

        $model = new \App\Models\MataPelajaran();
        $deletedNames = [];
        foreach ($ids as $id) {
            $old = $model->findById((int)$id);
            if ($old) {
                $deletedNames[] = $old['nama_mapel'] . " (" . $old['kode_mapel'] . ")";
            }
        }

        $db = \App\Core\Database::connect();
        try {
            $db->beginTransaction();
            foreach ($ids as $id) {
                $model->delete((int)$id);
            }
            $db->commit();

            $deletedDetail = implode(', ', $deletedNames);
            log_activity("Menghapus massal " . count($ids) . " mata pelajaran: " . $deletedDetail, 'siswa');

            push_notif(count($ids) . ' mata pelajaran berhasil dihapus.');
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
        }
        $this->redirect('mata-pelajaran');
    }
}