<?php

namespace App\Core;

class Router
{
    private array $routes = [
        // AUTH
        '' => ['App\\Controllers\\DashboardController', 'index'],
        'dashboard' => ['App\\Controllers\\DashboardController', 'index'],
        'login' => ['App\\Controllers\\AuthController', 'login'],
        'authenticate' => ['App\\Controllers\\AuthController', 'authenticate'],
        'logout' => ['App\\Controllers\\AuthController', 'logout'],

        // DATA SISWA
        'siswa'        => ['App\\Controllers\\SiswaController', 'index'],
        'siswa/store'  => ['App\\Controllers\\SiswaController', 'store'],
        'siswa/update' => ['App\\Controllers\\SiswaController', 'update'],
        'siswa/delete' => ['App\\Controllers\\SiswaController', 'delete'],
        'siswa/delete-batch' => ['App\\Controllers\\SiswaController', 'deleteBatch'],
        'siswa/import' => ['App\\Controllers\\SiswaController', 'import'],
        'siswa/template' => ['App\\Controllers\\SiswaController', 'template'],
        'nilai' => ['App\\Controllers\\NilaiController', 'index'],
        'nilai/store' => ['App\\Controllers\\NilaiController', 'store'],
        'nilai/get-riwayat' => ['App\\Controllers\\NilaiController', 'getRiwayatSiswa'],
        'nilai/get-mapel' => ['App\\Controllers\\NilaiController', 'getMapelByRiwayat'],
        'nilai/detail' => ['App\\Controllers\\NilaiController', 'detail'],
        'nilai/edit' => ['App\\Controllers\\NilaiController', 'edit'],
        'nilai/update' => ['App\\Controllers\\NilaiController', 'update'],
        'nilai/delete' => ['App\\Controllers\\NilaiController', 'delete'],
        'nilai/delete-batch' => ['App\\Controllers\\NilaiController', 'deleteBatch'],
        'nilai/import' => ['App\\Controllers\\NilaiController', 'import'],
        'nilai/template' => ['App\\Controllers\\NilaiController', 'template'],
        'nilai/cetak' => ['App\\Controllers\\NilaiController', 'cetakLeger'],
        'riwayat-kelas'               => ['App\\Controllers\\RiwayatKelasController', 'index'],
        'riwayat-kelas/store'         => ['App\\Controllers\\RiwayatKelasController', 'store'],
        'riwayat-kelas/update'        => ['App\\Controllers\\RiwayatKelasController', 'update'],
        'riwayat-kelas/delete'        => ['App\\Controllers\\RiwayatKelasController', 'delete'],
        'riwayat-kelas/delete-batch'  => ['App\\Controllers\\RiwayatKelasController', 'deleteBatch'],
        'riwayat-kelas/import'        => ['App\\Controllers\\RiwayatKelasController', 'import'],
        'riwayat-kelas/search-siswa'  => ['App\\Controllers\\RiwayatKelasController', 'searchSiswa'],
        'riwayat-kelas/template'      => ['App\\Controllers\\RiwayatKelasController', 'template'],
        'mata-pelajaran'        => ['App\\Controllers\\MataPelajaranController', 'index'],
        'mata-pelajaran/store'  => ['App\\Controllers\\MataPelajaranController', 'store'],
        'mata-pelajaran/update' => ['App\\Controllers\\MataPelajaranController', 'update'],
        'mata-pelajaran/delete' => ['App\\Controllers\\MataPelajaranController', 'delete'],
        'mata-pelajaran/delete-batch' => ['App\\Controllers\\MataPelajaranController', 'deleteBatch'],

        // KRITERIA
        'kriteria'        => ['App\\Controllers\\KriteriaController', 'index'],
        'kriteria/store'  => ['App\\Controllers\\KriteriaController', 'store'],
        'kriteria/update' => ['App\\Controllers\\KriteriaController', 'update'],
        'kriteria/delete' => ['App\\Controllers\\KriteriaController', 'delete'],
        'konversi'        => ['App\\Controllers\\KonversiController', 'index'],
        'konversi/store'  => ['App\\Controllers\\KonversiController', 'store'],
        'konversi/update' => ['App\\Controllers\\KonversiController', 'update'],
        'konversi/delete' => ['App\\Controllers\\KonversiController', 'delete'],

        // PERHITUNGAN
        'perhitungan/kelas'        => ['App\\Controllers\\PerhitunganKelasController',     'index'],
        'perhitungan/hitung-kelas' => ['App\\Controllers\\PerhitunganKelasController',     'hitung'],
        'perhitungan/detail-kelas' => ['App\\Controllers\\PerhitunganKelasController',     'detail'],
        'perhitungan/hapus-batch'  => ['App\\Controllers\\PerhitunganKelasController',     'hapusBatch'],
        'perhitungan/kelas-delete-batch' => ['App\\Controllers\\PerhitunganKelasController', 'deleteBatch'],
        'perhitungan/eligible'              => ['App\\Controllers\\PerhitunganEligibleController', 'index'],
        'perhitungan/eligible-hitung'       => ['App\\Controllers\\PerhitunganEligibleController', 'hitung'],
        'perhitungan/eligible-detail'       => ['App\\Controllers\\PerhitunganEligibleController', 'detail'],
        'perhitungan/eligible-delete'       => ['App\\Controllers\\PerhitunganEligibleController', 'delete'],
        'perhitungan/eligible-delete-batch' => ['App\\Controllers\\PerhitunganEligibleController', 'deleteBatch'],
        'perhitungan/eligible-get-siswa'    => ['App\\Controllers\\PerhitunganEligibleController', 'getSiswa'],

        // PDSS EXPORT
        'pdss/get-mapel'       => ['App\\Controllers\\PDSSController', 'getMapel'],
        'pdss/export-eligible' => ['App\\Controllers\\PDSSController', 'exportEligible'],
        'pdss/export-nilai'    => ['App\\Controllers\\PDSSController', 'exportNilai'],

        // HASIL AKHIR (Dedicated module)
        'hasil/kelas'       => ['App\\Controllers\\HasilController', 'kelas'],
        'hasil/kelas-lihat' => ['App\\Controllers\\HasilController', 'kelasLihat'],
        'hasil/eligible'       => ['App\\Controllers\\HasilController', 'eligible'],
        'hasil/eligible-lihat' => ['App\\Controllers\\HasilController', 'eligibleLihat'],
        'hasil/generate-pdf'   => ['App\\Controllers\\HasilController', 'generatePdf'],

        // LAPORAN
        'laporan'              => ['App\\Controllers\\LaporanController', 'index'],
        'laporan/detail'       => ['App\\Controllers\\LaporanController', 'detail'],
        'laporan/delete'       => ['App\\Controllers\\LaporanController', 'delete'],
        'laporan/delete-batch' => ['App\\Controllers\\LaporanController', 'deleteBatch'],
        'laporan/download'     => ['App\\Controllers\\LaporanController', 'download'],

        // AKUN
        'akun'         => ['App\\Controllers\\AkunController', 'index'],
        'akun/store'   => ['App\\Controllers\\AkunController', 'store'],
        'akun/update'  => ['App\\Controllers\\AkunController', 'update'],
        'akun/delete'  => ['App\\Controllers\\AkunController', 'delete'],

        // PROFILE
        'profile'                 => ['App\\Controllers\\ProfileController', 'index'],
        'profile/update'          => ['App\\Controllers\\ProfileController', 'update'],
        'profile/update-foto'     => ['App\\Controllers\\ProfileController', 'updateFoto'],
        'profile/delete-foto'     => ['App\\Controllers\\ProfileController', 'deleteFoto'],
        'profile/change-password' => ['App\\Controllers\\ProfileController', 'changePassword'],

        // NOTIFIKASI
        'notif/dismiss'   => ['App\\Controllers\\NotifController', 'dismiss'],
        'notif/clear-all' => ['App\\Controllers\\NotifController', 'clearAll'],
        'notif/add'       => ['App\\Controllers\\NotifController', 'add'],

        // LOG AKTIVITAS
        'log'             => ['App\\Controllers\\ActivityLogController', 'index'],
        'log/api-fetch'   => ['App\\Controllers\\ActivityLogController', 'apiFetch'],

    ];

    public function dispatch(): void
    {
        $path = $this->path();
        $route = $this->routes[$path] ?? null;

        if ($route === null) {
            http_response_code(404);
            echo 'Halaman tidak ditemukan.';
            return;
        }

        [$controllerClass, $method] = $route;
        $controller = new $controllerClass();
        $controller->{$method}();
    }

    private function path(): string
    {
        $url = $_GET['url'] ?? '';
        $url = trim($url, '/');

        return $url;
    }
}
