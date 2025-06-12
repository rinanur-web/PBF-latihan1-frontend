<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Mahasiswa</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #000;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 40px;
        }

        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo img {
            max-height: 60px;
        }

        .header-info {
            text-align: center;
            margin-bottom: 20px;
        }

        hr {
            border: 1px solid #000;
        }
    </style>
</head>
<body>

    <div class="header-info">
        <strong>Laporan Data Mahasiswa</strong><br>
        <small>Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</small>
    </div>

    <hr>

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIM</th>
                <th>Email</th>
                <th>Program Studi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mahasiswa as $index => $mhs)
                <tr>
                <td>{{ $mhs['nama'] }}</td>
                <td>{{ $mhs['nim'] }}</td>
                <td>{{ $mhs['email'] }}</td>
                <td>{{ $mhs['prodi'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Sistem Akademik</p>
    </div>

</body>
</html>
