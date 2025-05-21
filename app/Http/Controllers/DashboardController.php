<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $dosens = Http::get('http://localhost:8080/dosen')->json();
        $mahasiswas = Http::get('http://localhost:8080/mahasiswa')->json();

        $jumlahDosen = count($dosens);
        $jumlahMahasiswa = count($mahasiswas);

        return view('dashboard.index', compact('jumlahDosen', 'jumlahMahasiswa'));
    }
}
