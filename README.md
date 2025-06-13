Membuat file laravel

        composer create-project laravel/laravel namaprojek

menyalakan server

        php spark serve
        php artisan serve

membuat controller dan view

        php artisan make:controller MahasiswaController
        php artisan make:view nama_file

contoh membuat mahasiswacontroller

```php
        <?php

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
        }
```
pemanggilan bisa seperti ini
```php
         <a href="{{ route('mahasiswa.index') }}
         <a href="{{ url('/mahasiswa/tambah') }}" class="btn btn-primary mb-3">Tambah mahasiswa</a>
```
membuat menu dropdown di index

```php
         <div class="mb-4">
            <label for="program_studi" class="block text-gray-700 text-sm font-bold mb-2">Program Studi:</label>
            <select name="program_studi" id="program_studi" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="">Pilih Program Studi</option>
                @foreach ($programStudiOptions as $option)
                    <option value="{{ $option }}" {{ old('program_studi') == $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
        </div>
```

membuat menu dropdown di edit
```php
         <div class="mb-4">
                <label for="program_studi" class="block text-gray-700 text-sm font-bold mb-2">Program Studi:</label>
                <select name="program_studi" id="program_studi" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Pilih Program Studi</option>
                    @foreach ($programStudiOptions as $option)
                        <option value="{{ $option }}" {{ (old('program_studi', $mahasiswa['data']['program_studi'] ?? '') == $option) ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
```  
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
```php
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
```

untuk tabel yang memiliki fk

🧠 Model CI4
🔹 app/Models/RuanganModel.php
```php
        <?php
        
        namespace App\Models;
        
        use CodeIgniter\Model;
        
        class RuanganModel extends Model
        {
            protected $table = 'ruangan';
            protected $primaryKey = 'kode';
            protected $allowedFields = ['kode', 'nama_ruangan'];
            public $useTimestamps = false;
        }
 ```       
🔹 app/Models/MahasiswaModel.php
```php
        <?php
        
        namespace App\Models;
        
        use CodeIgniter\Model;
        
        class MahasiswaModel extends Model
        {
            protected $table = 'mahasiswa';
            protected $primaryKey = 'id';
            protected $allowedFields = ['nim', 'nama', 'email', 'prodi', 'kode_ruangan'];
            public $useTimestamps = false;
        
            // Relasi dengan ruangan
            public function withRuangan()
            {
                return $this->select('mahasiswa.*, ruangan.nama_ruangan')
                            ->join('ruangan', 'ruangan.kode = mahasiswa.kode_ruangan', 'left');
            }
        }
```
🧩 Controller CI4
🔹 app/Controllers/Mahasiswa.php
```php
        <?php
        
        namespace App\Controllers;
        
        use App\Models\MahasiswaModel;
        use CodeIgniter\RESTful\ResourceController;
        
        class Mahasiswa extends ResourceController
        {
            protected $modelName = MahasiswaModel::class;
            protected $format    = 'json';
        
            public function index()
            {
                $model = new MahasiswaModel();
                return $this->respond($model->withRuangan()->findAll());
            }
        
            public function show($id = null)
            {
                $model = new MahasiswaModel();
                $data = $model->withRuangan()->find($id);
                if (!$data) return $this->failNotFound('Data tidak ditemukan.');
                return $this->respond($data);
            }
        
            public function create()
            {
                $data = $this->request->getJSON(true);
                if ($this->model->insert($data)) {
                    return $this->respondCreated(['message' => 'Data mahasiswa ditambahkan']);
                }
                return $this->failValidationErrors($this->model->errors());
            }
        
            public function update($id = null)
            {
                $data = $this->request->getJSON(true);
                if ($this->model->update($id, $data)) {
                    return $this->respond(['message' => 'Data mahasiswa diperbarui']);
                }
                return $this->failValidationErrors($this->model->errors());
            }
        
            public function delete($id = null)
            {
                if ($this->model->delete($id)) {
                    return $this->respondDeleted(['message' => 'Data mahasiswa dihapus']);
                }
                return $this->failNotFound('Data tidak ditemukan.');
            }
        }
```
🔹 app/Controllers/Ruangan.php
```php
        <?php
        
        namespace App\Controllers;
        
        use App\Models\RuanganModel;
        use CodeIgniter\RESTful\ResourceController;
        
        class Ruangan extends ResourceController
        {
            protected $modelName = RuanganModel::class;
            protected $format    = 'json';
        
            public function index()
            {
                return $this->respond($this->model->findAll());
            }
        
            public function show($kode = null)
            {
                $data = $this->model->find($kode);
                if (!$data) return $this->failNotFound('Data ruangan tidak ditemukan.');
                return $this->respond($data);
            }
        
            public function create()
            {
                $data = $this->request->getJSON(true);
                if ($this->model->insert($data)) {
                    return $this->respondCreated(['message' => 'Ruangan ditambahkan']);
                }
                return $this->failValidationErrors($this->model->errors());
            }
        
            public function update($kode = null)
            {
                $data = $this->request->getJSON(true);
                if ($this->model->update($kode, $data)) {
                    return $this->respond(['message' => 'Ruangan diperbarui']);
                }
                return $this->failValidationErrors($this->model->errors());
            }
        
            public function delete($kode = null)
            {
                if ($this->model->delete($kode)) {
                    return $this->respondDeleted(['message' => 'Ruangan dihapus']);
                }
                return $this->failNotFound('Ruangan tidak ditemukan.');
            }
        }
```
🛣️ Route app/Config/Routes.php
Tambahkan:

```php
        // API Mahasiswa
        $routes->resource('mahasiswa', ['controller' => 'Mahasiswa']);
        
        // API Ruangan
        $routes->resource('ruangan', ['controller' => 'Ruangan']);
```

🧩 Contoh View mahasiswa/create.blade.php
```php
        @extends('dashboard.dashboard')
        
        @section('content')
        <h2>Tambah Mahasiswa</h2>
        
        <form action="{{ url('/mahasiswa') }}" method="POST">
            @csrf
        
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
        
            <div class="form-group">
                <label>NIM</label>
                <input type="text" name="nim" class="form-control" required>
            </div>
        
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
        
            <div class="form-group">
                <label>Prodi</label>
                <input type="text" name="prodi" class="form-control">
            </div>
        
            <div class="form-group">
                <label>Ruangan</label>
                <select name="kode_ruangan" class="form-control" required>
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach($ruanganList as $ruangan)
                        <option value="{{ $ruangan['kode'] }}">{{ $ruangan['nama_ruangan'] }}</option>
                    @endforeach
                </select>
            </div>
        
            <button class="btn btn-primary mt-2">Simpan</button>
        </form>
        @endsection
```
🧩 Contoh View mahasiswa/edit.blade.php
```php
        @extends('dashboard.dashboard')
        
        @section('content')
        <h2>Edit Mahasiswa</h2>
        
        <form action="{{ url('/mahasiswa/' . $mahasiswa['id']) }}" method="POST">
            @csrf
            @method('PUT')
        
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" value="{{ $mahasiswa['nama'] }}" required>
            </div>
        
            <div class="form-group">
                <label>NIM</label>
                <input type="text" name="nim" class="form-control" value="{{ $mahasiswa['nim'] }}" required>
            </div>
        
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ $mahasiswa['email'] }}" required>
            </div>
        
            <div class="form-group">
                <label>Prodi</label>
                <input type="text" name="prodi" class="form-control" value="{{ $mahasiswa['prodi'] }}">
            </div>
        
            <div class="form-group">
                <label>Ruangan</label>
                <select name="kode_ruangan" class="form-control" required>
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach($ruanganList as $ruangan)
                        <option value="{{ $ruangan['kode'] }}" {{ $ruangan['kode'] == $mahasiswa['kode_ruangan'] ? 'selected' : '' }}>
                            {{ $ruangan['nama_ruangan'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        
            <button class="btn btn-primary mt-2">Update</button>
        </form>
        @endsection
```
🧠 Controller Laravel (Frontend)
Contoh di MahasiswaController.php (bagian create dan edit):

```php
        public function create()
        {
            // Ambil data ruangan dari API
            $response = Http::get('http://localhost:8080/ruangan');
            $ruanganList = $response->json();
        
            return view('mahasiswa.create', compact('ruanganList'));
        }
        
        public function edit($id)
        {
            $mhs = Http::get("http://localhost:8080/mahasiswa/$id")->json();
            $ruanganList = Http::get("http://localhost:8080/ruangan")->json();
        
            return view('mahasiswa.edit', [
                'mahasiswa' => $mhs,
                'ruanganList' => $ruanganList
            ]);
        }
```

✅ Method store() (tambah mahasiswa)
```php
public function store(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:100',
        'nim' => 'required|string|max:20',
        'email' => 'required|email|max:100',
        'prodi' => 'required|string|max:50',
        'kode_ruangan' => 'required|string'
    ]);

    try {
        $response = Http::post('http://localhost:8080/mahasiswa', $request->all());

        if ($response->successful()) {
            return redirect('/mahasiswa')->with('success', 'Mahasiswa berhasil ditambahkan!');
        } else {
            $message = $response->json()['message'] ?? 'Gagal menyimpan data.';
            return back()->withInput()->withErrors(['api_error' => $message]);
        }
    } catch (\Exception $e) {
        return back()->withInput()->withErrors(['connection_error' => 'Gagal koneksi ke API: ' . $e->getMessage()]);
    }
}
```
✏️ Method update() (edit mahasiswa)
```php
public function update(Request $request, $id)
{
    $request->validate([
        'nama' => 'required|string|max:100',
        'nim' => 'required|string|max:20',
        'email' => 'required|email|max:100',
        'prodi' => 'required|string|max:50',
        'kode_ruangan' => 'required|string'
    ]);

    try {
        $response = Http::put("http://localhost:8080/mahasiswa/{$id}", $request->all());

        if ($response->successful()) {
            return redirect('/mahasiswa')->with('success', 'Mahasiswa berhasil diperbarui!');
        } else {
            $message = $response->json()['message'] ?? 'Gagal update data.';
            return back()->withInput()->withErrors(['api_error' => $message]);
        }
    } catch (\Exception $e) {
        return back()->withInput()->withErrors(['connection_error' => 'Gagal koneksi ke API: ' . $e->getMessage()]);
    }
}
```

Berikut ini adalah contoh backend di CodeIgniter 4 untuk mengelola mahasiswa dan ruangan, termasuk relasi foreign key kode_ruangan di tabel mahasiswa.

🧩 Struktur Tabel (MySQL)
Tabel ruangan

        CREATE TABLE ruangan (
            kode_ruangan VARCHAR(10) PRIMARY KEY,
            nama_ruangan VARCHAR(100) NOT NULL
        );

Tabel mahasiswa

        CREATE TABLE mahasiswa (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(100),
            nim VARCHAR(20),
            email VARCHAR(100),
            prodi VARCHAR(50),
            kode_ruangan VARCHAR(10),
            FOREIGN KEY (kode_ruangan) REFERENCES ruangan(kode_ruangan)
        );

📁 Model CodeIgniter
app/Models/MahasiswaModel.php
```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaModel extends Model
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'nim', 'email', 'prodi', 'kode_ruangan'];

    protected $useTimestamps = false;

    public function withRuangan()
    {
        return $this->select('mahasiswa.*, ruangan.nama_ruangan')
                    ->join('ruangan', 'ruangan.kode_ruangan = mahasiswa.kode_ruangan', 'left');
    }
}
```
app/Models/RuanganModel.php
```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class RuanganModel extends Model
{
    protected $table = 'ruangan';
    protected $primaryKey = 'kode_ruangan';
    protected $allowedFields = ['kode_ruangan', 'nama_ruangan'];

    protected $useAutoIncrement = false;
    public $returnType = 'array';
}
```
🎮 Controller CodeIgniter
app/Controllers/Mahasiswa.php
```php
<?php

namespace App\Controllers;
use App\Models\MahasiswaModel;
use CodeIgniter\RESTful\ResourceController;

class Mahasiswa extends ResourceController
{
    protected $modelName = 'App\Models\MahasiswaModel';
    protected $format = 'json';

    public function index()
    {
        return $this->respond($this->model->withRuangan()->findAll());
    }

    public function show($id = null)
    {
        $data = $this->model->withRuangan()->find($id);
        if (!$data) return $this->failNotFound('Mahasiswa tidak ditemukan');
        return $this->respond($data);
    }

    public function create()
    {
        $input = $this->request->getJSON(true);
        if ($this->model->insert($input)) {
            return $this->respondCreated(['message' => 'Mahasiswa berhasil ditambahkan']);
        }
        return $this->fail($this->model->errors());
    }

    public function update($id = null)
    {
        $input = $this->request->getJSON(true);
        if ($this->model->update($id, $input)) {
            return $this->respond(['message' => 'Mahasiswa berhasil diupdate']);
        }
        return $this->fail($this->model->errors());
    }

    public function delete($id = null)
    {
        if ($this->model->delete($id)) {
            return $this->respondDeleted(['message' => 'Mahasiswa berhasil dihapus']);
        }
        return $this->failNotFound('Data tidak ditemukan');
    }
}
```
🌐 Routes (CodeIgniter)

app/Config/Routes.php
```php
$routes->resource('mahasiswa'); // otomatis buat GET, POST, PUT, DELETE
$routes->get('ruangan', 'Ruangan::index'); // untuk fetch dropdown ruangan
```
Tambahan: Controller Ruangan
```php
<?php

namespace App\Controllers;
use App\Models\RuanganModel;
use CodeIgniter\RESTful\ResourceController;

class Ruangan extends ResourceController
{
    protected $modelName = 'App\Models\RuanganModel';
    protected $format = 'json';

    public function index()
    {
        return $this->respond($this->model->findAll());
    }
}
```
