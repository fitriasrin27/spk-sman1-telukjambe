<?php

namespace App\Core;

class RoleAccess
{
    /**
     * Peta Hak Akses Pusat
     * Mendefinisikan role mana saja yang boleh mengakses modul tertentu.
     */
    private static $map = [
        'dashboard'       => ['operator', 'kepala_sekolah', 'wakasek', 'tu', 'bk', 'wali_kelas'],
        'akun'            => ['operator'],
        'siswa'           => ['operator', 'tu', 'wali_kelas', 'wakasek', 'bk'],
        'riwayat-kelas'   => ['operator', 'tu', 'wali_kelas', 'wakasek', 'bk'],
        'mata-pelajaran'  => ['operator', 'tu', 'wakasek', 'bk', 'wali_kelas'],
        'nilai'           => ['operator', 'bk', 'wali_kelas', 'tu', 'wakasek'],
        'kriteria'        => ['operator', 'bk', 'wali_kelas'],
        'konversi'        => ['operator', 'bk', 'wali_kelas'],
        'perhitungan'     => ['operator', 'bk', 'wali_kelas', 'tu'],
        'hasil'           => ['operator', 'bk', 'wali_kelas', 'tu', 'wakasek'],
        'laporan'         => ['operator', 'kepala_sekolah', 'wakasek', 'bk', 'wali_kelas', 'tu'],
        'profile'         => ['operator', 'kepala_sekolah', 'wakasek', 'tu', 'bk', 'wali_kelas'],
        'log'             => ['operator'],
    ];

    /**
     * Fungsi untuk mengecek apakah user yang login punya akses ke modul tersebut.
     * @param string $module Nama modul (sesuai kunci di $map)
     * @return void
     */
    public static function check(string $module): void
    {
        $user = current_user();
        $role = $user['role'] ?? '';

        // Jika modul tidak ada di peta, anggap semua dilarang (aman)
        if (!isset(self::$map[$module])) {
            self::deny();
        }

        // Jika role user tidak ada di daftar akses modul tersebut
        if (!in_array($role, self::$map[$module])) {
            self::deny();
        }
    }

    /**
     * Fungsi untuk menolak akses dan melempar kembali ke Dashboard
     */
    private static function deny(): void
    {
        $_SESSION['error'] = "Maaf, Anda tidak memiliki hak akses untuk membuka halaman tersebut.";
        
        // Gunakan header redirect agar lebih cepat
        header("Location: " . url('dashboard'));
        exit();
    }
}