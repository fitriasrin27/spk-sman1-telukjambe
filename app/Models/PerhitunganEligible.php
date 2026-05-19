<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class PerhitunganEligible
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Mendapatkan daftar tahun ajaran untuk filter (Tahun dimana siswa berada di kelas XII)
     */
    public function getTahunAjaran(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT tahun_ajaran FROM riwayat_kelas WHERE kelas LIKE 'XII%' ORDER BY tahun_ajaran DESC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Mendapatkan daftar riwayat perhitungan eligible
     */
    public function getRiwayat(string $tahunAjaran = '', string $jurusan = '', int $limit = 10, int $offset = 0): array
    {
        $where = ["p.jenis_perhitungan = 'peringkat_eligible'"];
        $params = [];

        if ($tahunAjaran) {
            $where[] = "p.tahun_ajaran = :ta";
            $params[':ta'] = $tahunAjaran;
        }
        if ($jurusan) {
            $where[] = "p.jurusan = :jurusan";
            $params[':jurusan'] = $jurusan;
        }

        $sql = "SELECT p.*, u.nama as nama_user, u.role as role_user, u.foto as foto_user,
                       (SELECT COUNT(*) FROM hasil_perhitungan hp WHERE hp.id_perhitungan = p.id_perhitungan) as jumlah_siswa
                FROM perhitungan p
                LEFT JOIN users u ON p.id_user = u.id_user
                WHERE " . implode(" AND ", $where) . "
                ORDER BY p.tanggal_hitung DESC
                LIMIT :limit OFFSET :offset";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRiwayatTotal(string $tahunAjaran = '', string $jurusan = ''): int
    {
        $where = ["jenis_perhitungan = 'peringkat_eligible'"];
        $params = [];

        if ($tahunAjaran) {
            $where[] = "tahun_ajaran = :ta";
            $params[':ta'] = $tahunAjaran;
        }
        if ($jurusan) {
            $where[] = "jurusan = :jurusan";
            $params[':jurusan'] = $jurusan;
        }

        $sql = "SELECT COUNT(*) FROM perhitungan WHERE " . implode(" AND ", $where);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Alias dari getRiwayatTotal() — dipanggil oleh controller untuk pagination riwayat.
     */
    public function countRiwayat(string $tahunAjaran = '', string $jurusan = ''): int
    {
        return $this->getRiwayatTotal($tahunAjaran, $jurusan);
    }

    // =========================================================
    // PESERTA ELIGIBLE
    // =========================================================

    /**
     * Ambil semua siswa kelas XII suatu jurusan per kelas
     * Digunakan untuk menampilkan tabel seleksi peserta
     */
    public function getSiswaKelasXII(string $tahunAjaran, string $jurusan, string $kelas = ''): array
    {
        $where  = ["rk.tahun_ajaran = :ta",
                   "(rk.kelas LIKE 'XII%' OR rk.kelas LIKE '12%')",
                   "rk.kelas LIKE :jurusan"];
        $params = [':ta' => $tahunAjaran, ':jurusan' => '%' . $jurusan . '%', ':exactJurusan' => $jurusan];

        if ($kelas !== '') {
            $where[]          = 'rk.kelas = :kelas';
            $params[':kelas'] = $kelas;
        }

        $mapelUtama = $this->getMapelUtamaCodes($jurusan);
        $mapelLower = array_map('strtolower', $mapelUtama);
        $mapelList  = "'" . implode("','", $mapelLower) . "'";

        $sql = "
            SELECT DISTINCT s.id_siswa, s.nama, s.nisn, s.nis, rk.kelas, rk.id_riwayat,
                   (SELECT 1 FROM peserta_eligible pe
                    WHERE pe.id_riwayat = rk.id_riwayat
                      AND pe.id_perhitungan = (
                          SELECT id_perhitungan FROM perhitungan 
                          WHERE tahun_ajaran = :ta AND jurusan = :exactJurusan 
                            AND jenis_perhitungan = 'peringkat_eligible'
                          ORDER BY id_perhitungan DESC LIMIT 1
                      )
                    LIMIT 1) as terdaftar,
                   (SELECT COUNT(*) FROM nilai n
                    JOIN mata_pelajaran mp ON n.id_mapel = mp.id_mapel
                    WHERE n.id_riwayat IN (
                        SELECT id_riwayat FROM riwayat_kelas 
                        WHERE id_siswa = s.id_siswa AND semester <= 5
                    )
                    AND (LOWER(mp.kode_mapel) IN ($mapelList) OR LOWER(mp.nama_mapel) IN ($mapelList))
                    AND n.nilai > 0
                   ) as jumlah_nilai,
                   (SELECT COUNT(*) FROM absensi
                    WHERE id_riwayat IN (
                        SELECT id_riwayat FROM riwayat_kelas 
                        WHERE id_siswa = s.id_siswa AND semester <= 5
                    )
                   ) as jumlah_absensi
            FROM riwayat_kelas rk
            JOIN siswa s ON rk.id_siswa = s.id_siswa
            WHERE " . implode(' AND ', $where) . "
            ORDER BY rk.kelas ASC, s.nama ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil daftar nama kelas XII untuk suatu jurusan (untuk tab tombol kelas)
     */
    public function getDaftarKelasXII(string $tahunAjaran, string $jurusan): array
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT kelas FROM riwayat_kelas
            WHERE tahun_ajaran = :ta
              AND (kelas LIKE 'XII%' OR kelas LIKE '12%')
              AND kelas LIKE :jurusan
            ORDER BY kelas ASC
        ");
        $stmt->execute([':ta' => $tahunAjaran, ':jurusan' => '%' . $jurusan . '%']);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Hitung total siswa kelas XII suatu jurusan (untuk kalkulasi kuota 40%)
     */
    public function countTotalSiswaJurusan(string $tahunAjaran, string $jurusan): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT s.id_siswa)
            FROM riwayat_kelas rk
            JOIN siswa s ON rk.id_siswa = s.id_siswa
            WHERE rk.tahun_ajaran = :ta
              AND (rk.kelas LIKE 'XII%' OR rk.kelas LIKE '12%')
              AND rk.kelas LIKE :jurusan
        ");
        $stmt->execute([':ta' => $tahunAjaran, ':jurusan' => '%' . $jurusan . '%']);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Simpan daftar peserta eligible sebagai LOG per perhitungan.
     * Dipanggil SETELAH id_perhitungan dibuat, agar bisa dijadikan histori.
     * Tidak menghapus data lama.
     */
    public function savePendaftar(int $idPerhitungan, string $tahunAjaran, string $jurusan, array $idRiwayatList): array
    {
        if (empty($idRiwayatList)) return [];

        $mapping = [];
        $stmtIns = $this->db->prepare("
            INSERT INTO peserta_eligible (id_riwayat, tahun_ajaran, jurusan, id_perhitungan)
            VALUES (:id_riwayat, :ta, :jurusan, :id_p)
        ");
        foreach ($idRiwayatList as $idRiwayat) {
            $stmtIns->execute([
                ':id_riwayat'  => (int)$idRiwayat,
                ':ta'          => $tahunAjaran,
                ':jurusan'     => $jurusan,
                ':id_p'        => $idPerhitungan,
            ]);
            $mapping[$idRiwayat] = (int)$this->db->lastInsertId();
        }
        return $mapping;
    }

    /**
     * Ambil daftar siswa yang sudah terdaftar sebagai peserta eligible
     */
    public function getPendaftar(string $tahunAjaran, string $jurusan): array
    {
        $stmt = $this->db->prepare("
            SELECT pe.id_peserta, pe.id_riwayat, s.id_siswa, s.nama, s.nisn, s.nis, rk.kelas
            FROM peserta_eligible pe
            JOIN riwayat_kelas rk ON pe.id_riwayat = rk.id_riwayat
            JOIN siswa s ON rk.id_siswa = s.id_siswa
            WHERE pe.tahun_ajaran = :ta AND pe.jurusan = :jurusan
            ORDER BY s.nama ASC
        ");
        $stmt->execute([':ta' => $tahunAjaran, ':jurusan' => $jurusan]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Hitung jumlah pendaftar yang sudah dipilih
     */
    public function countPendaftar(string $tahunAjaran, string $jurusan): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM peserta_eligible
            WHERE tahun_ajaran = :ta AND jurusan = :jurusan
        ");
        $stmt->execute([':ta' => $tahunAjaran, ':jurusan' => $jurusan]);
        return (int)$stmt->fetchColumn();
    }

    // =========================================================
    // SAW CALCULATION
    // =========================================================

    /**
     * Ambil data siswa dari id_riwayat list untuk SAW.
     * Menerima list id_riwayat XII (kelas XII semester 5 atau 6).
     */
    public function getSiswaFromRiwayatList(array $idRiwayatList): array
    {
        if (empty($idRiwayatList)) return [];

        $placeholders = implode(',', array_fill(0, count($idRiwayatList), '?'));
        $stmt = $this->db->prepare("
            SELECT rk.id_riwayat as riwayat_xii, rk.id_siswa, s.nama, s.nisn, s.nis, rk.kelas
            FROM riwayat_kelas rk
            JOIN siswa s ON rk.id_siswa = s.id_siswa
            WHERE rk.id_riwayat IN ($placeholders)
            ORDER BY s.nama ASC
        ");
        $stmt->execute(array_values($idRiwayatList));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDataSiswaEligible(string $tahunAjaran, string $jurusan): array
    {
        $tahunAjaran = trim($tahunAjaran);
        $jurusan     = trim($jurusan);

        // 1. Ambil siswa dari tabel peserta_eligible via id_riwayat
        // Juga ambil id_peserta untuk disimpan di hasil_perhitungan
        $stmtSiswa = $this->db->prepare("
            SELECT pe.id_peserta, rk.id_siswa, s.nama, s.nisn, s.nis, rk.kelas
            FROM peserta_eligible pe
            JOIN riwayat_kelas rk ON pe.id_riwayat = rk.id_riwayat
            JOIN siswa s ON rk.id_siswa = s.id_siswa
            WHERE pe.tahun_ajaran = :ta AND pe.jurusan = :jurusan
            ORDER BY s.nama ASC
        ");
        $stmtSiswa->execute([':ta' => $tahunAjaran, ':jurusan' => $jurusan]);
        $daftarSiswa = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);




        $results = [];
        foreach ($daftarSiswa as $siswa) {
            $idSiswa = $siswa['id_siswa'];
            
            // 2. Ambil seluruh riwayat_kelas siswa ini (untuk mendapatkan id_riwayat di smt 1-5)
            $stmtRiwayat = $this->db->prepare("
                SELECT id_riwayat, semester 
                FROM riwayat_kelas 
                WHERE id_siswa = :id_siswa 
                AND semester <= 5
                ORDER BY semester ASC
            ");
            $stmtRiwayat->execute([':id_siswa' => $idSiswa]);
            $listRiwayat = $stmtRiwayat->fetchAll(PDO::FETCH_ASSOC);
            $idsRiwayat = array_column($listRiwayat, 'id_riwayat');

            if (empty($idsRiwayat)) continue;

            $placeholders = implode(',', array_fill(0, count($idsRiwayat), '?'));

            // 3. Hitung C1: Total Nilai 13 Mapel Utama
            $mapelUtama = $this->getMapelUtamaCodes($jurusan);
            $mapelUtamaLower = array_map('strtolower', $mapelUtama);
            $mapelPlaceholders = implode(',', array_fill(0, count($mapelUtamaLower), '?'));

            $sqlC1 = "
                SELECT SUM(n.nilai) as total_akademik
                FROM nilai n
                JOIN mata_pelajaran mp ON n.id_mapel = mp.id_mapel
                WHERE n.id_riwayat IN ($placeholders)
                  AND (LOWER(mp.kode_mapel) IN ($mapelPlaceholders) OR LOWER(mp.nama_mapel) IN ($mapelPlaceholders))
            ";
            $stmtC1 = $this->db->prepare($sqlC1);
            $stmtC1->execute(array_merge($idsRiwayat, $mapelUtamaLower, $mapelUtamaLower));
            $totalC1 = (float) $stmtC1->fetchColumn();
            $c1 = $totalC1 / 65; // 13 Mapel x 5 Semester


            // 4. Hitung C2: Total Absensi
            $sqlC2 = "SELECT SUM(sakit + izin + alpa) FROM absensi WHERE id_riwayat IN ($placeholders)";
            $stmtC2 = $this->db->prepare($sqlC2);
            $stmtC2->execute($idsRiwayat);
            $c2 = (int) $stmtC2->fetchColumn();

            // 5. Hitung C3: Total Poin Ekskul (Menggunakan konversi kriteria C3)
            $sqlC3 = "
                SELECT COALESCE(SUM(kv.nilai_konversi), 0)
                FROM ekstrakurikuler e
                JOIN konversi_nilai kv ON kv.nilai_asli = e.predikat
                JOIN kriteria kr ON kr.id_kriteria = kv.id_kriteria AND kr.kode_kriteria = 'C3'
                WHERE e.id_riwayat IN ($placeholders)
            ";
            $stmtC3 = $this->db->prepare($sqlC3);
            $stmtC3->execute($idsRiwayat);
            $c3 = (float) $stmtC3->fetchColumn();

            // 6. Hitung C4: Total Poin Prestasi (Menggunakan konversi kriteria C4)
            $sqlC4 = "
                SELECT COALESCE(SUM(kv.nilai_konversi), 0)
                FROM prestasi p
                JOIN konversi_nilai kv ON kv.nilai_asli = p.tingkat
                JOIN kriteria kr ON kr.id_kriteria = kv.id_kriteria AND kr.kode_kriteria = 'C4'
                WHERE p.id_riwayat IN ($placeholders)
            ";
            $stmtC4 = $this->db->prepare($sqlC4);
            $stmtC4->execute($idsRiwayat);
            $c4 = (float) $stmtC4->fetchColumn();

            $results[] = [
                'id_siswa'   => $idSiswa,
                'id_peserta' => $siswa['id_peserta'],
                'nama'       => $siswa['nama'],
                'nisn'       => $siswa['nisn'],
                'nis'        => $siswa['nis'],
                'kelas'      => $siswa['kelas'],
                'c1_total'   => $totalC1,
                'c1_avg'     => $totalC1 / 65,
                'c2_raw'     => $c2,
                'c3_raw'     => $c3,
                'c4_raw'     => $c4
            ];
        }

        return $results;
    }

    /**
     * Daftar Kode Mapel Utama (13 Mapel)
     * Sesuai permintaan user: PAIDBP, PPDK, BInd, MU, SI, BIng, SB, PJODK, PDK + Peminatan
     */
    private function getMapelUtamaCodes(string $jurusan): array
    {
        // Mendukung variasi kode dari user (PAIDBP, BInd, BIng) 
        // DAN kode standar dari audit (PADBP, BIND, BING)
        $mapelUmum = [
            'PADBP', 'PAIDBP', 
            'PPDK', 
            'BIND', 'BInd', 
            'MU', 
            'SI', 
            'BING', 'BIng', 
            'SB', 
            'PJODK', 
            'PDK'
        ];
        
        $mapelMIPA = ['MP', 'B', 'F', 'K'];
        $mapelIPS  = ['S', 'G', 'E', 'SOS', 'SEJ', 'GEO', 'EKO', 'SOSIOLOGI'];

        $peminatan = (strtoupper($jurusan) === 'MIPA') ? $mapelMIPA : $mapelIPS;
            
        return array_merge($mapelUmum, $peminatan);
    }



    /**
     * Pastikan kolom bobot ada di tabel perhitungan untuk menghindari error Unknown Column.
     */
    private function checkColumns(): void
    {
        $cols = ['bobot_c1', 'bobot_c2', 'bobot_c3', 'bobot_c4'];
        foreach ($cols as $col) {
            $check = $this->db->query("SHOW COLUMNS FROM perhitungan LIKE '$col'")->fetch();
            if (!$check) {
                $this->db->exec("ALTER TABLE perhitungan ADD COLUMN $col DECIMAL(5,2) DEFAULT 0");
            }
        }
    }

    /**
     * Hitung SAW untuk Eligible.
     * Menerima id_riwayat list langsung dari controller - tidak perlu query peserta_eligible.
     */
    public function hitungSAW(string $tahunAjaran, string $jurusan, array $idRiwayatList): int
    {
        $this->checkColumns(); // Auto-migration if columns missing

        if (empty($idRiwayatList)) {
            throw new \RuntimeException("Tidak ada siswa yang dipilih (idRiwayatList kosong).");
        }

        // 1. Resolve siswa dari id_riwayat yang dipilih
        $daftarSiswa = $this->getSiswaFromRiwayatList($idRiwayatList);
        if (empty($daftarSiswa)) {
            throw new \RuntimeException("Tidak ada data siswa ditemukan dari " . count($idRiwayatList) . " id_riwayat yang dipilih.");
        }

        // 2. Ambil bobot kriteria
        $stmtK  = $this->db->query("SELECT kode_kriteria, bobot FROM kriteria ORDER BY kode_kriteria ASC");
        $bobot  = [];
        foreach ($stmtK->fetchAll(PDO::FETCH_ASSOC) as $k) {
            $bobot[$k['kode_kriteria']] = (float)$k['bobot'];
        }
        // Fallback defaults
        if (!isset($bobot['C1'])) $bobot['C1'] = 0.90;
        if (!isset($bobot['C2'])) $bobot['C2'] = 0.05;
        if (!isset($bobot['C3'])) $bobot['C3'] = 0.03;
        if (!isset($bobot['C4'])) $bobot['C4'] = 0.02;

        // 3. Hitung C1-C4 per siswa (5 semester)
        $results = [];
        foreach ($daftarSiswa as $siswa) {
            $idSiswa = $siswa['id_siswa'];

            // Ambil riwayat semester 1-5
            $stmtR = $this->db->prepare("
                SELECT id_riwayat FROM riwayat_kelas
                WHERE id_siswa = :id AND semester <= 5
                ORDER BY semester ASC
            ");
            $stmtR->execute([':id' => $idSiswa]);
            $listRiwayat = $stmtR->fetchAll(PDO::FETCH_COLUMN);

            if (empty($listRiwayat)) continue;

            $ph = implode(',', array_fill(0, count($listRiwayat), '?'));

            // C1: Total nilai akademik (13 mapel x 5 semester)
            $mapelUtama = $this->getMapelUtamaCodes($jurusan);
            $mapelLower = array_map('strtolower', $mapelUtama);
            $phM = implode(',', array_fill(0, count($mapelLower), '?'));
            $sqlC1 = "SELECT COALESCE(SUM(n.nilai), 0)
                      FROM nilai n
                      JOIN riwayat_kelas rk ON n.id_riwayat = rk.id_riwayat
                      JOIN mata_pelajaran mp ON n.id_mapel = mp.id_mapel
                      WHERE n.id_riwayat IN ($ph)
                        AND (LOWER(mp.kode_mapel) IN ($phM) OR LOWER(mp.nama_mapel) IN ($phM))
                        AND n.nilai > 0";
            $stmtC1 = $this->db->prepare($sqlC1);
            $stmtC1->execute(array_merge($listRiwayat, $mapelLower, $mapelLower));
            $totalC1 = (float)$stmtC1->fetchColumn();
            $c1Avg   = $totalC1 / 65;

            // C2: Total absensi
            $stmtC2 = $this->db->prepare("SELECT COALESCE(SUM(sakit + izin + alpa), 0) FROM absensi WHERE id_riwayat IN ($ph)");
            $stmtC2->execute($listRiwayat);
            $c2 = (int)$stmtC2->fetchColumn();

            // C3: Poin ekskul
            $stmtC3 = $this->db->prepare("
                SELECT COALESCE(SUM(kv.nilai_konversi), 0)
                FROM ekstrakurikuler e
                JOIN konversi_nilai kv ON kv.nilai_asli = e.predikat
                JOIN kriteria kr ON kr.id_kriteria = kv.id_kriteria AND kr.kode_kriteria = 'C3'
                WHERE e.id_riwayat IN ($ph)
            ");
            $stmtC3->execute($listRiwayat);
            $c3 = (float)$stmtC3->fetchColumn();

            // C4: Poin prestasi
            $stmtC4 = $this->db->prepare("
                SELECT COALESCE(SUM(kv.nilai_konversi), 0)
                FROM prestasi p
                JOIN konversi_nilai kv ON kv.nilai_asli = p.tingkat
                JOIN kriteria kr ON kr.id_kriteria = kv.id_kriteria AND kr.kode_kriteria = 'C4'
                WHERE p.id_riwayat IN ($ph)
            ");
            $stmtC4->execute($listRiwayat);
            $c4 = (float)$stmtC4->fetchColumn();

            $results[] = [
                'id_siswa' => $idSiswa,
                'nama'     => $siswa['nama'],
                'c1_total' => $totalC1,
                'c1_avg'   => $c1Avg,
                'c2_raw'   => $c2,
                'c3_raw'   => $c3,
                'c4_raw'   => $c4,
            ];
        }

        if (empty($results)) {
            throw new \RuntimeException("Tidak ada data nilai ditemukan untuk siswa yang dipilih.");
        }

        // 4. Normalisasi SAW
        $maxC1 = max(array_column($results, 'c1_total')) ?: 1;
        $maxC3 = max(array_column($results, 'c3_raw')) ?: 1;
        $maxC4 = max(array_column($results, 'c4_raw')) ?: 1;

        $finalData = [];
        foreach ($results as $s) {
            // n1: Akademik = nilai_akademik / max_akademik
            $n1 = $s['c1_total'] / $maxC1;
            
            // n2: Absensi = (max_akademik - absen_i) / max_akademik
            $n2 = ($maxC1 - $s['c2_raw']) / $maxC1;
            
            // n3: Ekskul = (akademik_i + ekskul_i) / (max_akademik + max_ekskul)
            $n3 = ($s['c1_total'] + $s['c3_raw']) / ($maxC1 + $maxC3);
            
            // n4: Prestasi = (akademik_i + prestasi_i) / (max_akademik + max_prestasi)
            $n4 = ($s['c1_total'] + $s['c4_raw']) / ($maxC1 + $maxC4);
            
            $skor = ($n1 * $bobot['C1']) + ($n2 * $bobot['C2']) + ($n3 * $bobot['C3']) + ($n4 * $bobot['C4']);
            
            $finalData[] = array_merge($s, [
                'n1' => $n1, 'n2' => $n2, 'n3' => $n3, 'n4' => $n4, 'skor' => $skor
            ]);
        }
        usort($finalData, fn($a, $b) => $b['skor'] <=> $a['skor']);

        // 5. Kuota eligible
        $totalSiswaJurusan = $this->countTotalSiswaJurusan($tahunAjaran, $jurusan);
        $kuotaEligible     = (int)round($totalSiswaJurusan * 0.40);

        // 6. Simpan perhitungan + hasil ke DB (transaksi utama)
        $this->db->beginTransaction();
        try {
            $idUser = (int)(current_user()['id_user'] ?? 1);

            // Insert batch perhitungan + simpan bobot saat ini
            $stmtP = $this->db->prepare("
                INSERT INTO perhitungan (
                    jenis_perhitungan, tahun_ajaran, jurusan, id_user, tanggal_hitung,
                    bobot_c1, bobot_c2, bobot_c3, bobot_c4
                )
                VALUES ('peringkat_eligible', :ta, :jurusan, :user, NOW(), :b1, :b2, :b3, :b4)
            ");
            $stmtP->execute([
                ':ta'      => $tahunAjaran,
                ':jurusan' => $jurusan,
                ':user'    => $idUser,
                ':b1'      => $bobot['C1'],
                ':b2'      => $bobot['C2'],
                ':b3'      => $bobot['C3'],
                ':b4'      => $bobot['C4']
            ]);
            $idPerhitungan = (int)$this->db->lastInsertId();

            // Insert log ke peserta_eligible agar dapat ID Peserta (MENGHUBUNGKAN SISWA KE HISTORI)
            $pesertaMap = $this->savePendaftar($idPerhitungan, $tahunAjaran, $jurusan, $idRiwayatList);

            // Bikin mapping dari id_siswa ke id_peserta
            $siswaToPeserta = [];
            foreach ($daftarSiswa as $ds) {
                // $ds['riwayat_xii'] dari getSiswaFromRiwayatList adalah id_riwayat kelas 12
                $rXii = $ds['riwayat_xii'];
                $siswaToPeserta[$ds['id_siswa']] = $pesertaMap[$rXii] ?? null;
            }

            // Insert hasil per siswa lengkap dengan id_peserta
            $stmtH = $this->db->prepare("
                INSERT INTO hasil_perhitungan (
                    id_perhitungan, id_siswa, id_peserta,
                    c1_nilai_akademik, n_c1,
                    c2_absensi, n_c2,
                    c3_ekskul, n_c3,
                    c4_prestasi, n_c4,
                    nilai_preferensi, ranking,
                    status_eligible, rata_rata_akademik
                ) VALUES (
                    :id_p, :id_s, :id_peserta,
                    :c1r, :n1,
                    :c2r, :n2,
                    :c3r, :n3,
                    :c4r, :n4,
                    :skor, :rank,
                    :status, :avg
                )
            ");
            foreach ($finalData as $i => $f) {
                $rankNum = $i + 1;
                $stmtH->execute([
                    ':id_p'       => $idPerhitungan,
                    ':id_s'       => $f['id_siswa'],
                    ':id_peserta' => $siswaToPeserta[$f['id_siswa']] ?? null,
                    ':c1r'        => $f['c1_total'], ':n1' => $f['n1'],
                    ':c2r'        => $f['c2_raw'],   ':n2' => $f['n2'],
                    ':c3r'        => $f['c3_raw'],   ':n3' => $f['n3'],
                    ':c4r'        => $f['c4_raw'],   ':n4' => $f['n4'],
                    ':skor'       => $f['skor'],
                    ':rank'       => $rankNum,
                    ':status'     => $rankNum <= $kuotaEligible ? 'ya' : 'tidak',
                    ':avg'        => $f['c1_avg']
                ]);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }

        return $idPerhitungan;
    }



    /**
     * Ambil hasil perhitungan berdasarkan ID Perhitungan
     */
    public function getHasilTotalByIdPerhitungan(int $idPerhitungan, string $search = ''): int
    {
        $sql = "SELECT COUNT(*) FROM hasil_perhitungan h 
                JOIN siswa s ON h.id_siswa = s.id_siswa 
                WHERE h.id_perhitungan = :id";
        $params = [':id' => $idPerhitungan];

        if ($search) {
            $sql .= " AND (s.nama LIKE :q OR s.nisn LIKE :q OR s.nis LIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getHasilByIdPerhitungan(int $idPerhitungan, int $limit = 0, int $offset = 0, string $search = ''): array
    {
        // 1. Ambil data batch/riwayat
        $stmtP = $this->db->prepare("
            SELECT p.*, u.nama as nama_user, u.role as role_user, u.foto as foto_user
            FROM perhitungan p
            LEFT JOIN users u ON p.id_user = u.id_user
            WHERE p.id_perhitungan = :id
        ");
        $stmtP->execute([':id' => $idPerhitungan]);
        $riwayat = $stmtP->fetch(PDO::FETCH_ASSOC);

        if (!$riwayat) return [];

        // Hitung kuota eligible dari database
        $stmtK = $this->db->prepare("SELECT COUNT(*) FROM hasil_perhitungan WHERE id_perhitungan = :id AND status_eligible = 'ya'");
        $stmtK->execute([':id' => $idPerhitungan]);
        $riwayat['kuota_eligible'] = (int)$stmtK->fetchColumn();

        // 2. Ambil data hasil siswa beserta kelas XII-nya
        $sqlH = "SELECT h.*, s.nama, s.nisn, s.nis,
                        (SELECT rk.kelas FROM riwayat_kelas rk 
                         WHERE rk.id_siswa = h.id_siswa 
                           AND rk.kelas LIKE 'XII%'
                         ORDER BY rk.semester DESC LIMIT 1) as kelas
                 FROM hasil_perhitungan h
                 JOIN siswa s ON h.id_siswa = s.id_siswa
                 WHERE h.id_perhitungan = :id";
        
        $params = [':id' => $idPerhitungan];

        if ($search) {
            $sqlH .= " AND (s.nama LIKE :q OR s.nisn LIKE :q OR s.nis LIKE :q)";
            $params[':q'] = '%' . $search . '%';
        }

        $sqlH .= " ORDER BY h.ranking ASC";
        
        if ($limit > 0) {
            $sqlH .= " LIMIT :limit OFFSET :offset";
        }

        $stmtH = $this->db->prepare($sqlH);
        $stmtH->bindValue(':id', $idPerhitungan, PDO::PARAM_INT);
        if ($search) {
            $stmtH->bindValue(':q', $params[':q']);
        }
        if ($limit > 0) {
            $stmtH->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmtH->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        $stmtH->execute();
        $hasil = $stmtH->fetchAll(PDO::FETCH_ASSOC);

        return [
            'riwayat' => $riwayat,
            'hasil'   => $hasil
        ];
    }

    /**
     * Ambil bobot kriteria untuk tampilan detail
     */
    public function getKriteria(): array
    {
        $stmt = $this->db->query("SELECT * FROM kriteria ORDER BY kode_kriteria ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
