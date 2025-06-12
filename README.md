Membuat file laravel

        composer create-project laravel/laravel namaprojek

menyalakan server

        php spark serve
        php artisan serve

membuat controller dan view

        php artisan make:controller MahasiswaController
        php artisan make:view nama_file

contoh membuat mahasiswacontroller

        <pre lang="php">```<?php

        namespace App\Http\Controllers;
        
        use Illuminate\Http\Request;
        use Illuminate\Support\Facades\Http;
        use App\Models\User; // Pastikan model User ada
        use App\Models\dataMahasiswa; 
        use Illuminate\Support\Facades\Log; // Untuk logging error
        
        use Barryvdh\DomPDF\Facade\Pdf; // tambahkan di atas
        
        class MahasiswaController extends Controller
        {
            // Base URL untuk API Mahasiswa
            private $apiUrl = 'http://localhost:8080/mahasiswa';
        
            /**
             * Menampilkan daftar semua mahasiswa.
             *
             * @return \Illuminate\View\View
             */
            public function index()
            {
                $mahasiswa = [];
                $errors = [];
        
                try {
                    // Ambil data mahasiswa
                    $mahasiswaResponse = Http::get($this->apiUrl);
                    if ($mahasiswaResponse->successful()) {
                        $mahasiswa = $mahasiswaResponse->json(); // Ambil data JSON dari response
                    } else {
                        // Tangani error jika API tidak mengembalikan status sukses
                        $errors['mahasiswa_api_error'] = $mahasiswaResponse->json()['message'] ?? 'Gagal mengambil data mahasiswa.';
                    }
                } catch (\Exception $e) {
                    // Tangani exception jika ada masalah koneksi atau lainnya
                    $errors['mahasiswa_connection_error'] = 'Tidak dapat terhubung ke API mahasiswa: ' . $e->getMessage();
                }
        
                return view('mahasiswa.index', compact('mahasiswa', 'errors'));
            }
        
            /**
             * Menampilkan detail satu mahasiswa.
             *
             * @param string $id NPM mahasiswa
             * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
             */
            public function show($id)
            {
                try {
                    $response = Http::get("{$this->apiUrl}/{$id}");
        
                    if ($response->successful()) {
                        $mahasiswa = $response->json();
                        return view('mahasiswa.show', compact('mahasiswa'));
                    } else {
                        $errorMessage = $response->json()['message'] ?? 'Mahasiswa tidak ditemukan.';
                        return redirect()->route('mahasiswa.index')->with('error', $errorMessage);
                    }
                } catch (\Exception $e) {
                    return redirect()->route('mahasiswa.index')->with('error', 'Tidak dapat terhubung ke API: ' . $e->getMessage());
                }
            }
        
            /**
             * Menampilkan form untuk membuat mahasiswa baru.
             *
             * @return \Illuminate\View\View
             */
            public function create()
            {
                // Daftar program studi sesuai dengan ENUM di tabel database
                $programStudiOptions = [
                    'D3 Teknik Elektronika',
                    'D3 Teknik Listrik',
                    'D3 Teknik Informatika',
                    'D3 Teknik Mesin',
                    'D4 Teknik Pengendalian Pencemaran Lingkungan',
                    'D4 Pengembangan Produk Agroindustri',
                    'D4 Teknologi Rekayasa Energi Terbarukan',
                    'D4 Rekayasa Kimia Industri',
                    'D4 Teknologi Rekayasa Mekatronika',
                    'D4 Rekayasa Keamanan Siber',
                    'D4 Teknologi Rekayasa Multimedia',
                    'D4 Akuntansi Lembaga Keuangan Syariah',
                    'D4 Rekayasa Perangkat Lunak',
                ];
                return view('mahasiswa.create', compact('programStudiOptions'));
            }
        
            /**
             * Menyimpan mahasiswa baru ke API.
             *
             * @param \Illuminate\Http\Request $request
             * @return \Illuminate\Http\RedirectResponse
             */
            public function store(Request $request)
            {
                // Validasi input dari form
                $request->validate([
                    'npm' => 'required|string|max:9',
                    'nama_mahasiswa' => 'required|string|max:50',
                    'program_studi' => 'required|string',
                    'judul_skripsi' => 'required|string|max:150',
                    'email' => 'required|email|max:30',
                ]);
        
                try {
                    $response = Http::post($this->apiUrl, $request->all());
        
                    if ($response->successful()) {
                        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan!');
                    } else {
                        $errorMessage = $response->json()['message'] ?? 'Gagal menambahkan data mahasiswa.';
                        return redirect()->back()->withInput()->withErrors(['api_error' => $errorMessage]);
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->withInput()->withErrors(['connection_error' => 'Tidak dapat terhubung ke API: ' . $e->getMessage()]);
                }
            }
        
            /**
             * Menampilkan form untuk mengedit mahasiswa.
             *
             * @param string $id NPM mahasiswa
             * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
             */
            public function edit($id)
            {
                try {
                    $response = Http::get("{$this->apiUrl}/{$id}");
        
                    if ($response->successful()) {
                        $mahasiswa = $response->json();
                        $programStudiOptions = [
                            'D3 Teknik Elektronika', 'D3 Teknik Listrik', 'D3 Teknik Informatika', 'D3 Teknik Mesin',
                            'D4 Teknik Pengendalian Pencemaran Lingkungan', 'D4 Pengembangan Produk Agroindustri',
                            'D4 Teknologi Rekayasa Energi Terbarukan', 'D4 Rekayasa Kimia Industri',
                            'D4 Teknologi Rekayasa Mekatronika', 'D4 Rekayasa Keamanan Siber',
                            'D4 Teknologi Rekayasa Multimedia', 'D4 Akuntansi Lembaga Keuangan Syariah',
                            'D4 Rekayasa Perangkat Lunak',
                        ];
                        return view('mahasiswa.edit', compact('mahasiswa', 'programStudiOptions'));
                    } else {
                        $errorMessage = $response->json()['message'] ?? 'Mahasiswa tidak ditemukan.';
                        return redirect()->route('mahasiswa.index')->with('error', $errorMessage);
                    }
                } catch (\Exception $e) {
                    return redirect()->route('mahasiswa.index')->with('error', 'Tidak dapat terhubung ke API: ' . $e->getMessage());
                }
            }
        
            /**
             * Mengupdate data mahasiswa melalui API.
             *
             * @param \Illuminate\Http\Request $request
             * @param string $id NPM mahasiswa
             * @return \Illuminate\Http\RedirectResponse
             */
            public function update(Request $request, $id)
            {
                // Validasi input
                $request->validate([
                    'nama_mahasiswa' => 'required|string|max:50',
                    'program_studi' => 'required|string',
                    'judul_skripsi' => 'required|string|max:150',
                    'email' => 'required|email|max:30',
                ]);
        
                try {
                    // Perhatikan bahwa NPM tidak diupdate karena itu adalah primary key
                    $response = Http::put("{$this->apiUrl}/{$id}", $request->except('npm')); // Kirim semua data kecuali NPM
        
                    if ($response->successful()) {
                        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui!');
                    } else {
                        $errorMessage = $response->json()['message'] ?? 'Gagal memperbarui data mahasiswa.';
                        return redirect()->back()->withInput()->withErrors(['api_error' => $errorMessage]);
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->withInput()->withErrors(['connection_error' => 'Tidak dapat terhubung ke API: ' . $e->getMessage()]);
                }
            }
        
            public function unduhMahasiswa($npm)
            {
                // 1. Ambil detail mahasiswa dari database lokal Anda berdasarkan $kode_mahasiswa
                $unduhMahasiswa = dataMahasiswa::where('npm', $npm)->first();
        
                // Jika mahasiswa tidak ditemukan, kembalikan error
                if (!$unduhMahasiswa) {
                    return back()->with('error', 'mahasiswa dengan NPM ' . $npm . ' tidak ditemukan.');
                }
        
                // 2. Buat view untuk PDF. Contoh: resources/views/pdfs/detail_mahasiswa.blade.php
                $pdf = PDF::loadView('mahasiswa.cetak', compact('unduhMahasiswa'));
        
                // 3. Kembalikan PDF sebagai unduhan
                return $pdf->download('mahasiswa.cetak_' . $npm . '.pdf');
            }
        
            /**
             * Menghapus data mahasiswa melalui API.
             *
             * @param string $id NPM mahasiswa
             * @return \Illuminate\Http\RedirectResponse
             */
            public function destroy($id)
            {
                try {
                    $response = Http::delete("{$this->apiUrl}/{$id}");
        
                    if ($response->successful()) {
                        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus!');
                    } else {
                        $errorMessage = $response->json()['message'] ?? 'Gagal menghapus data mahasiswa.';
                        return redirect()->route('mahasiswa.index')->with('error', $errorMessage);
                    }
                } catch (\Exception $e) {
                    return redirect()->route('mahasiswa.index')->with('error', 'Tidak dapat terhubung ke API: ' . $e->getMessage());
                }
            }
        }```</pre>

membuat menu dropdown di index

         <div class="mb-4">
            <label for="program_studi" class="block text-gray-700 text-sm font-bold mb-2">Program Studi:</label>
            <select name="program_studi" id="program_studi" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="">Pilih Program Studi</option>
                @foreach ($programStudiOptions as $option)
                    <option value="{{ $option }}" {{ old('program_studi') == $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
        </div>

membuat menu dropdown di edit

         <div class="mb-4">
                <label for="program_studi" class="block text-gray-700 text-sm font-bold mb-2">Program Studi:</label>
                <select name="program_studi" id="program_studi" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Pilih Program Studi</option>
                    @foreach ($programStudiOptions as $option)
                        <option value="{{ $option }}" {{ (old('program_studi', $mahasiswa['data']['program_studi'] ?? '') == $option) ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>



        
export pdf

        composer require barryvdh/laravel-dompdf 
        contoh pemanggilan data:
         @foreach($matkul as $index => $m)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $m['kode_matkul'] }}</td>
                    <td>{{ $m['nama_matkul'] }}</td>
                    <td>{{ $m['sks'] }}</td>
                </tr>
                @endforeach

menambahkan function exportpdf di controller

            public function exportPdf()
    {
        $response = Http::get('http://localhost:8080/matkul');
        if ($response->successful()) {
            $matkul = collect($response->json());
            $pdf = Pdf::loadView('pdf.cetak', compact('matkul')); 
            return $pdf->download('matkul.pdf');
        } else {
            return back()->with('error', 'Gagal mengambil data untuk PDF');
        }
    }
        
