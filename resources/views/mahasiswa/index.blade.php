@extends('dashboard.dashboard')

@section('content')
<h2>Data Mahasiswa</h2>

<a href="{{ url('/mahasiswa/tambah') }}" class="btn btn-primary mb-3">Tambah mahasiswa</a>
<a href="{{ url('/mahasiswa/cetak') }}" target="_blank" class="btn btn-primary">Cetak PDF</a>


<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIM</th>
            <th>Email</th>
            <th>Prodi</th>
            <th>Aksi</th>
            <th>Cetak</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mahasiswas as $mahasiswa)
        <tr>
            <td>{{ $mahasiswa['nama'] }}</td>
            <td>{{ $mahasiswa['nim'] }}</td>
            <td>{{ $mahasiswa['email'] }}</td>
            <td>{{ $mahasiswa['prodi'] }}</td>
            <td>
<a href="{{ url('/mahasiswa/' . $mahasiswa['id'] . '/edit') }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ url('/mahasiswa/' . $mahasiswa['id']) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</button>
</form>
            </td>           
    <td>
    <a href="{{ url('/mahasiswa/' . $mahasiswa['id'] . '/cetak') }}" target="_blank" class="btn btn-success btn-sm">Cetak</a>
</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
