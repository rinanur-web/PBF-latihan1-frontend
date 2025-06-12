@extends('dashboard.dashboard')

@section('content')
<h2>Data Dosen</h2>

<a href="{{ url('/dosen/tambah') }}" class="btn btn-primary mb-3">Tambah Dosen</a>


<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama</th>
            <th>NIDN</th>
            <th>Email</th>
            <th>Prodi</th>
            <th>Aksi</th>
            <th>Cetak</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dosens as $dosen)
        <tr>
            <td>{{ $dosen['nama'] }}</td>
            <td>{{ $dosen['nidn'] }}</td>
            <td>{{ $dosen['email'] }}</td>
            <td>{{ $dosen['prodi'] }}</td>
            <td>
                <a href="{{ url('/dosen/' . $dosen['id'] . '/edit') }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ url('/dosen/' . $dosen['id']) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</button>
</form>
            </td>
             <td>
    <a href="{{ url('/dosen/' . $dosen['id'] . '/cetak') }}" target="_blank" class="btn btn-success btn-sm">Cetak</a>
</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
