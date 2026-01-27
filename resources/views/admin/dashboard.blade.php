<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Guide Me</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 600px;
        }
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 36px;
        }
        .welcome {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-danger {
            background: #ef4444;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>👨‍💼 Admin Dashboard</h1>
        <p class="welcome">Selamat datang, <strong>{{ auth()->user()->name }}</strong>!</p>
        
        <div class="btn-group">
            <a href="{{ route('admin.pemilik.index') }}" class="btn btn-primary">
                📋 Kelola Pemilik Wisata
            </a>
            
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-danger">
                    🚪 Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>

