@extends('dashboard.dashboard')

@section('content')
<h2>Edit mahasiswa</h2>

<form action="{{ url('/mahasiswa/' . $mahasiswa['id']) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama" value="{{ $mahasiswa['nama'] }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>NIM</label>
        <input type="text" name="nim" value="{{ $mahasiswa['nim'] }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" value="{{ $mahasiswa['email'] }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>Prodi</label>
        <input type="text" name="prodi" value="{{ $mahasiswa['prodi'] }}" class="form-control">
    </div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
