<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Dashboard;

class DashboardController extends Controller
{
    public function index(): void
    {
        require_login();

        $dashboard = new Dashboard();
        $tahunAjaran = $_GET['tahun_ajaran'] ?? null;
        $semester = isset($_GET['semester']) ? (int)$_GET['semester'] : null;

        $this->view('dashboard/index', [
            'title' => 'Home',
            'totalSiswa' => $dashboard->totalSiswa(),
            'totalSiswaKelasXii' => $dashboard->totalSiswaKelasXii(),
            'kesiapanData' => $dashboard->getKesiapanData($tahunAjaran, $semester),
            'distribusiEligible' => $dashboard->distribusiEligible(),
            'kriteria' => $dashboard->getKriteria(),
            'progresRanking' => $dashboard->getProgresRankingKelas($tahunAjaran, $semester),
            'daftarTahunAjaran' => $dashboard->getDaftarTahunAjaran(),
            'tahunAjaran' => $tahunAjaran,
            'semester' => $semester
        ], 'layouts/app');
    }
}
