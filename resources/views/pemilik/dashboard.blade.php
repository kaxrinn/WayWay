<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemilik Wisata Dashboard - Guide Me</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
            color: #f5576c;
            margin-bottom: 10px;
            font-size: 36px;
        }
        .welcome {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }
        .btn {
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
            background: #ef4444;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏢 Pemilik Wisata Dashboard</h1>
        <p class="welcome">Selamat datang, <strong>{{ auth()->user()->name }}</strong>!</p>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn">
                🚪 Logout
            </button>
        </form>
    </div>
</body>
</html>