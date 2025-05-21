@extends('dashboard.dashboard')

@section('content')
<h2>Edit Dosen</h2>

<form action="{{ url('/dosen/' . $dosen['id']) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label>Nama</label>
        <input type="text" name="nama" value="{{ $dosen['nama'] }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>NIDN</label>
        <input type="text" name="nidn" value="{{ $dosen['nidn'] }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" value="{{ $dosen['email'] }}" class="form-control">
    </div>
    <div class="mb-3">
        <label>Prodi</label>
        <input type="text" name="prodi" value="{{ $dosen['prodi'] }}" class="form-control">
    </div>
    <button class="btn btn-primary">Update</button>
</form>
@endsection
