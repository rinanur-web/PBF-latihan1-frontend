<!DOCTYPE html>
<html>
<head>
    <title>Data Mahasiswa</title>
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
        <tr>
        <td>: {{ $mahasiswa['nama'] }}</td>
        <td>: {{ $mahasiswa['nim'] }}</td>
        <td>: {{ $mahasiswa['email'] }}</td>
        <td>: {{ $mahasiswa['prodi'] }}</td>
        </tr>
        </tbody>
    </table>
     <div class="footer">
        <p>&copy; {{ date('Y') }} Sistem Akademik</p>
    </div>
</body>
</html>
