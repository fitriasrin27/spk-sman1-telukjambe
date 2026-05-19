-- File ini berisi data akun default untuk memudahkan login pertama kali
-- Password untuk SEMUA akun di bawah ini adalah: password

INSERT INTO users (nama, username, password, password_plain, role, posisi) VALUES
('Administrator', 'operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'password', 'operator', 'Admin Sistem'),
('Kepala Sekolah', 'kepsek', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'password', 'kepala_sekolah', 'Kepala SMAN 1 Telukjambe'),
('Wakil Kepala Sekolah', 'wakasek', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'password', 'wakasek', 'Wakasek Kurikulum'),
('Staf Tata Usaha', 'tu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'password', 'tu', 'Staf Administrasi TU'),
('Guru BK', 'bk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'password', 'bk', 'Koordinator BK'),
('Wali Kelas', 'walas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'password', 'wali_kelas', 'Wali Kelas XII MIPA 1');
