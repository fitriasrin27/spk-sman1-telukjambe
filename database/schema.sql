CREATE DATABASE IF NOT EXISTS spk_sman1_telukjambe;
USE spk_sman1_telukjambe;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    password_plain VARCHAR(255) NULL,
    role ENUM('operator', 'wali_kelas', 'bk', 'tu', 'wakasek', 'kepala_sekolah') NOT NULL,
    posisi VARCHAR(100) NULL,
    last_login DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE siswa (
    id_siswa INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nisn VARCHAR(20) NOT NULL UNIQUE,
    nis VARCHAR(20) NOT NULL UNIQUE,
    jenis_kelamin ENUM('L', 'P') NOT NULL
);

CREATE TABLE riwayat_kelas (
    id_riwayat INT AUTO_INCREMENT PRIMARY KEY,
    id_siswa INT NOT NULL,
    tahun_ajaran VARCHAR(20) NOT NULL,
    kelas VARCHAR(30) NOT NULL,
    semester TINYINT NOT NULL,
    FOREIGN KEY (id_siswa) REFERENCES siswa(id_siswa) ON DELETE CASCADE,
    UNIQUE (id_siswa, tahun_ajaran, semester)
);

CREATE TABLE mata_pelajaran (
    id_mapel INT AUTO_INCREMENT PRIMARY KEY,
    kode_mapel VARCHAR(20) NOT NULL,
    nama_mapel VARCHAR(100) NOT NULL,
    tingkat ENUM('X', 'XI', 'XII') NOT NULL,
    jurusan VARCHAR(20) NOT NULL,
    UNIQUE (kode_mapel, tingkat, jurusan)
);

CREATE TABLE nilai (
    id_nilai INT AUTO_INCREMENT PRIMARY KEY,
    id_riwayat INT NOT NULL,
    id_mapel INT NOT NULL,
    nilai DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (id_riwayat) REFERENCES riwayat_kelas(id_riwayat) ON DELETE CASCADE,
    FOREIGN KEY (id_mapel) REFERENCES mata_pelajaran(id_mapel) ON DELETE CASCADE,
    UNIQUE (id_riwayat, id_mapel)
);

CREATE TABLE absensi (
    id_absensi INT AUTO_INCREMENT PRIMARY KEY,
    id_riwayat INT NOT NULL,
    sakit INT DEFAULT 0,
    izin INT DEFAULT 0,
    alpa INT DEFAULT 0,
    FOREIGN KEY (id_riwayat) REFERENCES riwayat_kelas(id_riwayat) ON DELETE CASCADE,
    UNIQUE (id_riwayat)
);

CREATE TABLE ekstrakurikuler (
    id_ekskul INT AUTO_INCREMENT PRIMARY KEY,
    id_riwayat INT NOT NULL,
    nama_ekskul VARCHAR(100) NOT NULL,
    predikat VARCHAR(10) NOT NULL,
    FOREIGN KEY (id_riwayat) REFERENCES riwayat_kelas(id_riwayat) ON DELETE CASCADE
);

CREATE TABLE prestasi (
    id_prestasi INT AUTO_INCREMENT PRIMARY KEY,
    id_riwayat INT NOT NULL,
    nama_prestasi VARCHAR(150) NOT NULL,
    tingkat VARCHAR(50) NOT NULL,
    keterangan TEXT NULL,
    FOREIGN KEY (id_riwayat) REFERENCES riwayat_kelas(id_riwayat) ON DELETE CASCADE
);

CREATE TABLE kriteria (
    id_kriteria INT AUTO_INCREMENT PRIMARY KEY,
    kode_kriteria VARCHAR(10) NOT NULL UNIQUE,
    nama_kriteria VARCHAR(100) NOT NULL,
    atribut ENUM('benefit', 'cost') NOT NULL,
    bobot DECIMAL(5,2) NOT NULL
);

CREATE TABLE konversi_nilai (
    id_konversi INT AUTO_INCREMENT PRIMARY KEY,
    id_kriteria INT NOT NULL,
    nilai_asli VARCHAR(50) NOT NULL,
    nilai_konversi DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (id_kriteria) REFERENCES kriteria(id_kriteria) ON DELETE CASCADE,
    UNIQUE (id_kriteria, nilai_asli)
);

CREATE TABLE peserta_eligible (
    id_peserta INT AUTO_INCREMENT PRIMARY KEY,
    id_riwayat INT NOT NULL,
    status_bersedia ENUM('ya', 'tidak') DEFAULT 'ya',
    tanggal_pilih DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_riwayat) REFERENCES riwayat_kelas(id_riwayat) ON DELETE CASCADE,
    UNIQUE (id_riwayat)
);

CREATE TABLE perhitungan (
    id_perhitungan INT AUTO_INCREMENT PRIMARY KEY,
    jenis_perhitungan ENUM('peringkat_kelas', 'eligible') NOT NULL,
    tahun_ajaran VARCHAR(20) NOT NULL,
    kelas VARCHAR(30) NULL,
    jurusan VARCHAR(20) NULL,
    semester_target TINYINT NULL,
    tanggal_hitung DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_user INT NOT NULL,
    catatan TEXT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE RESTRICT
);

CREATE TABLE hasil_perhitungan (
    id_hasil INT AUTO_INCREMENT PRIMARY KEY,
    id_perhitungan INT NOT NULL,
    id_siswa INT NOT NULL,
    id_peserta INT NULL,
    c1_nilai_akademik DECIMAL(10,2) NOT NULL DEFAULT 0,
    c2_absensi DECIMAL(10,2) NOT NULL DEFAULT 0,
    c3_ekskul DECIMAL(10,2) NOT NULL DEFAULT 0,
    c4_prestasi DECIMAL(10,2) NOT NULL DEFAULT 0,
    n_c1 DECIMAL(10,6) NOT NULL DEFAULT 0,
    n_c2 DECIMAL(10,6) NOT NULL DEFAULT 0,
    n_c3 DECIMAL(10,6) NOT NULL DEFAULT 0,
    n_c4 DECIMAL(10,6) NOT NULL DEFAULT 0,
    nilai_preferensi DECIMAL(10,6) NOT NULL DEFAULT 0,
    rata_rata_akademik DECIMAL(8,2) NOT NULL DEFAULT 0,
    ranking INT NOT NULL,
    status_eligible ENUM('ya', 'tidak') NULL,
    FOREIGN KEY (id_perhitungan) REFERENCES perhitungan(id_perhitungan) ON DELETE CASCADE,
    FOREIGN KEY (id_siswa) REFERENCES siswa(id_siswa) ON DELETE CASCADE,
    FOREIGN KEY (id_peserta) REFERENCES peserta_eligible(id_peserta) ON DELETE SET NULL
);

CREATE TABLE laporan (
    id_laporan INT AUTO_INCREMENT PRIMARY KEY,
    id_perhitungan INT NOT NULL,
    id_user INT NOT NULL,
    jenis_laporan ENUM('peringkat_kelas', 'eligible') NOT NULL,
    orientasi ENUM('portrait', 'landscape') NOT NULL,
    komponen_laporan TEXT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    tanggal_buat DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_perhitungan) REFERENCES perhitungan(id_perhitungan) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE RESTRICT
);

INSERT INTO kriteria (kode_kriteria, nama_kriteria, atribut, bobot) VALUES
('C1', 'Nilai Akademik', 'benefit', 0.60),
('C2', 'Absensi', 'cost', 0.03),
('C3', 'Ekstrakurikuler', 'benefit', 0.10),
('C4', 'Prestasi', 'benefit', 0.27);

INSERT INTO konversi_nilai (id_kriteria, nilai_asli, nilai_konversi) VALUES
(3, 'SB', 4),
(3, 'B', 3),
(3, 'C', 2),
(3, 'K', 1),
(4, 'Internasional', 20),
(4, 'Nasional', 15),
(4, 'Provinsi', 10),
(4, 'Kabupaten/Kota', 5);

