<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MahasiswaModel;

class MahasiswaController extends Controller
{
    public function index()
    {
        $response = Http::get('http://eval_pbf_frontend.test/mahasiswa');
        $mahasiswas = $response->json();
        return view('mahasiswa.index', compact('mahasiswas'));
    }

    public function create()
    {
        return view('mahasiswa.tambah');
    }

    public function store(Request $request)
    {
        Http::post('http://eval_pbf_frontend.test/mahasiswa', $request->all());
        return redirect('/mahasiswa')->with('success', 'Mahasiswa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $response = Http::get("http://eval_pbf_frontend.test/mahasiswa/{$id}");
        $mahasiswa = $response->json();
        return view('mahasiswa.update', compact('mahasiswa'));
    }

    public function update(Request $request, $id)
    {
        Http::put("http://eval_pbf_frontend.test/mahasiswa/{$id}", $request->all());
        return redirect('/mahasiswa')->with('success', 'Mahasiswa berhasil diupdate');
    }

    public function destroy($id)
    {
        Http::delete("http://eval_pbf_frontend.test/mahasiswa/{$id}");
        return redirect('/mahasiswa')->with('success', 'Mahasiswa berhasil dihapus');
    }

    public function cetakPDF()
    {
        $response = Http::get('http://eval_pbf_frontend.test/mahasiswa');
        $mahasiswa = $response->json();

        $pdf = Pdf::loadView('mahasiswa.cetak', compact('mahasiswa'));
        return $pdf->download('laporan-mahasiswa.pdf');
    }

   public function cetakSatuPDF($id)
{
    $response = Http::get("http://eval_pbf_frontend.test/mahasiswa/{$id}");
    $mahasiswa = $response->json();
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('mahasiswa.cetak_satu', compact('mahasiswa'));
    return $pdf->download("mahasiswa-{$id}.pdf");
}



}
