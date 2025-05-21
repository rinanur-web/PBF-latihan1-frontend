@extends('dashboard.dashboard')

@section('content')
<h2>Tambah mahasiswa</h2>

<form action="{{ url('/mahasiswa') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>nim</label>
        <input type="text" name="nim" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Prodi</label>
        <input type="text" name="prodi" class="form-control" required>
    </div>
    <button class="btn btn-success">Simpan</button>
</form>
@endsection
