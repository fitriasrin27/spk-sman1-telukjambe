# 🎓 Sistem Pendukung Keputusan (SPK) - SMAN 1 Telukjambe

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap)

Aplikasi **Sistem Pendukung Keputusan (SPK)** berbasis web untuk menentukan pemeringkatan siswa berprestasi dan pemeringkatan kuota *Eligible* SNBP. Sistem ini dibangun menggunakan metode perhitungan **SAW (Simple Additive Weighting)** untuk menjamin transparansi, kecepatan, dan akurasi dalam penentuan peringkat akademik siswa.

---

## ✨ Fitur Unggulan

- **Sistem Role-Based Access Control (RBAC):** Akses multi-user yang ketat dengan batasan hak akses sesuai jabatan (Operator, Kepala Sekolah, Wakasek, TU, Wali Kelas, dan Guru BK).
- **Mesin Kalkulasi Otomatis (SAW):** Perhitungan matriks keputusan secara otomatis dari nilai rapor, absensi, hingga prestasi non-akademik.
- **Export Laporan (PDF & Excel):** Pembuatan dokumen cetak resmi sekolah menggunakan standar kop surat.
- **Manajemen Nilai Terpusat:** Riwayat akademik lengkap untuk 5 semester pertama.
- **Import/Upload Data Massal:** Kemudahan input ratusan data siswa dan nilai dengan sekali upload file Excel (`.xlsx`).
- **Log Aktivitas Real-Time:** Pemantauan seluruh aktivitas pengguna secara langsung (*live monitoring*) dengan deteksi browser, IP address, 5 kartu metrik harian, filter lengkap, dan kebijakan pembersihan otomatis (maks. 25.000 data, dibersihkan tiap Juni untuk log > 1 tahun).


---

## 🛠️ Teknologi & *Library* (Vendor)

Aplikasi ini dirancang menggunakan arsitektur **MVC (Model-View-Controller)** *native* tanpa *framework* berat untuk menjaga performa tetap ringan dan cepat.

- **Inti Sistem:** PHP Native (OOP MVC)
- **Database:** MySQL / MariaDB
- **Tampilan:** HTML5, CSS3, JavaScript, Bootstrap 5
- **Library Tambahan (via Composer):**
  - [`dompdf/dompdf`](https://github.com/dompdf/dompdf) - Digunakan untuk men-generate (merender) tampilan HTML menjadi dokumen PDF.
  - [`phpoffice/phpspreadsheet`](https://github.com/PHPOffice/PhpSpreadsheet) - Digunakan untuk memproses import (baca) dan export (tulis) file format Microsoft Excel (`.xlsx`).

---

## 🚀 Panduan Instalasi (Setup Lengkap)

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di komputer lokal (localhost) kamu.

### 1. Persiapan Kebutuhan Server
Pastikan komputermu sudah ter-install aplikasi web server seperti **Laragon**, **XAMPP**, atau **MAMP**. Diwajibkan menggunakan versi PHP 7.4 ke atas.

### 2. Download (Clone) dan Install Library
Buka terminal/CMD, arahkan ke dalam folder direktori web kamu (misalnya `htdocs` atau `www`), lalu jalankan:

```bash
git clone https://github.com/fitriasrin27/spk-sman1-telukjambe.git spk-sman1-telukjambe
cd spk-sman1-telukjambe
```

Karena *vendor* (alat pendukung pihak ketiga) disembunyikan untuk efisiensi repositori, kamu **wajib** mendownloadnya secara otomatis dengan menjalankan:
```bash
composer install
```

### 3. Setup Database
- Buka aplikasi **phpMyAdmin** atau HeidiSQL di `http://localhost/phpmyadmin`.
- Buat sebuah *database* baru (kosong) bernama: `spk_sman1_telukjambe`.
- Cari menu **Import**, lalu masukkan 2 file SQL yang ada di dalam folder `database/`:
  1.  Import file `database/schema.sql` (Untuk membangun struktur/kerangka tabel).
  2.  Import file `database/akun-default.sql` (Untuk memasukkan daftar akun bawaan).

### 4. Menjalankan Aplikasi
Aplikasi ini tidak bisa dijalankan menggunakan ekstensi klik-kanan "Live Server" biasa. Kamu harus menggunakan *web server* asli.

**Cara Akses:**
Jika kamu menggunakan Laragon/XAMPP, langsung akses URL berikut di browsermu:
👉 `http://localhost/spk-sman1-telukjambe/public` (atau sesuaikan dengan port yang dimiliki oleh web server tersebut, misalnya `http://localhost:8080/spk-sman1-telukjambe/public`)

---

## 🔐 Daftar Akun Default (Akses Percobaan)

Kamu bisa masuk menggunakan berbagai hak akses berikut untuk melihat batasan fitur yang berbeda-beda. **Kunci sandi (password) untuk semua akun di bawah ini adalah: `password`**.

| Nama Akun Default | Username | Level Akses (Role) | Keterangan Wewenang Utama |
| :--- | :--- | :--- | :--- |
| **Administrator** | `operator` | Operator | Memiliki akses penuh CRUD ke seluruh menu dan pengaturan sistem. |
| **Kepala Sekolah** | `kepsek` | Kepala Sekolah | Akses "Hanya Baca" (*View Only*) dan memantau/mencetak hasil laporan akhir. |
| **Wakil Kepala Sekolah**| `wakasek`| Wakasek | Sama dengan Kepsek, fokus pada hasil pemeringkatan dan kontrol data siswa. |
| **Staf Tata Usaha** | `tu` | Tata Usaha | Mengelola administrasi Data Master (Siswa, Kelas, Mapel) secara penuh dan dapat memproses hasil perhitungan. |
| **Guru BK** | `bk` | Bimbingan Konseling | Bertanggung jawab penuh atas penilaian Akademik (Nilai Rapor) dan Non-Akademik (Ekstrakurikuler, Prestasi). Mengatur Bobot Kriteria & Konversi Nilai, serta mengeksekusi perhitungan peringkat Eligible SNBP. |
| **Wali Kelas** | `walas` | Wali Kelas | Bertanggung jawab menginput Data Nilai, Absensi, dan Prestasi siswa, serta mengeksekusi perhitungan pemeringkatan kelas. |

---

## 📁 Penjelasan Struktur Folder

- `app/` -> Otak utama aplikasi yang dipecah jadi `Controllers`, `Models`, `Views`, `Core`, dan `Helpers`.
- `database/` -> Tempat penyimpanan cetak biru dan instalasi *database* bawaan.
- `public/` -> Folder akar bagi pengunjung web. Berisi file statis (CSS/JS/Gambar) dan pintu masuk utama (`index.php`).
- `storage/sessions/` -> Folder khusus menyimpan riwayat login (aman dan tersembunyi).
- `vendor/` -> (Muncul setelah instalasi Composer) Gudang penyimpanan alat pendukung pihak ketiga (PDF & Excel).

---
*Dibuat untuk kebutuhan Skripsi/Tugas Akhir SMAN 1 Telukjambe.*