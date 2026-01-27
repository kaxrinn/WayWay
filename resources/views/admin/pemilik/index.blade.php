<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemilik Wisata - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            color: #667eea;
            font-size: 28px;
        }
        .btn {
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-warning {
            background: #f59e0b;
            color: white;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-sm {
            padding: 8px 15px;
            font-size: 13px;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        table tbody tr:hover {
            background: #f9fafb;
        }
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .action-btns {
            display: flex;
            gap: 8px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        .empty-state i {
            font-size: 80px;
            margin-bottom: 20px;
            color: #d1d5db;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>📋 Kelola Pemilik Wisata</h1>
                <p style="color: #666; font-size: 14px; margin-top: 5px;">
                    Admin: <strong>{{ auth()->user()->name }}</strong>
                </p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('admin.pemilik.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Pemilik Wisata
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <div class="card">
            @if($pemilikWisata->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Terdaftar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemilikWisata as $index => $pemilik)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $pemilik->name }}</strong>
                        </td>
                        <td>{{ $pemilik->email }}</td>
                        <td>{{ $pemilik->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> Aktif
                            </span>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="{{ route('admin.pemilik.edit', $pemilik->id) }}" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" 
                                      action="{{ route('admin.pemilik.destroy', $pemilik->id) }}"
                                      onsubmit="return confirm('Yakin hapus pemilik wisata ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <i class="fas fa-users-slash"></i>
                <h3>Belum Ada Pemilik Wisata</h3>
                <p>Klik tombol "Tambah Pemilik Wisata" untuk menambahkan data baru</p>
            </div>
            @endif
        </div>
    </div>
</body>
</html>