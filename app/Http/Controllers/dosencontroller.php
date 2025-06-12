<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DosenController extends Controller
{
    public function index()
{
   // Ambil data langsung dari API
    $response = Http::get('http://eval_pbf_frontend.test/dosen');

    if ($response->successful()) {
        $dosens = $response->json(); // karena isinya array langsung
    } else {
        $dosens = [];
    }

    return view('dosen.index', compact('dosens'));
}

    public function create()
    {
        return view('dosen.tambah');
    }

    public function store(Request $request)
    {
        Http::post('http://eval_pbf_frontend.test/dosen', $request->all());
        return redirect('/dosen')->with('success', 'Dosen berhasil ditambahkan');
    }

    public function edit($id)
    {
        $response = Http::get("http://eval_pbf_frontend.test/dosen/{$id}");
    
    // Ambil langsung semua isi JSON tanpa bungkus 'data'
    $dosen = $response->json();

    return view('dosen.update', compact('dosen'));
    }

    public function update(Request $request, $id)
    {
        Http::put("http://eval_pbf_frontend.test/dosen/{$id}", $request->all());
        return redirect('/dosen')->with('success', 'Dosen berhasil diupdate');
    }

    public function destroy($id)
    {
        Http::delete("http://eval_pbf_frontend.test/dosen/{$id}");
        return redirect('/dosen')->with('success', 'Dosen berhasil dihapus');
    }

    public function cetakSatuPDF($id)
{
    $response = Http::get("http://eval_pbf_frontend.test/dosen/{$id}");
    $dosen = $response->json();
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dosen.cetak_satu', compact('dosen'));
    return $pdf->download("dosen-{$id}.pdf");
}

}
