<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pemilik Wisata - Admin</title>
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
            max-width: 600px;
            margin: 0 auto;
        }
        .card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .input-wrapper {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            transition: color 0.3s;
        }
        .toggle-password:hover {
            color: #667eea;
        }
        .error-text {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
            display: block;
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
            font-size: 14px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            width: 100%;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6b7280;
            color: white;
            margin-top: 10px;
            width: 100%;
        }
        .password-requirements {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 13px;
        }
        .password-requirements p {
            color: #6b7280;
            margin-bottom: 5px;
        }
        .password-requirements i {
            color: #667eea;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>➕ Tambah Pemilik Wisata</h1>
            <p class="subtitle">Isi form di bawah untuk menambahkan pemilik wisata baru</p>

            <form method="POST" action="{{ route('admin.pemilik.store') }}">
                @csrf

                <!-- Nama Lengkap -->
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" placeholder="Nama Lengkap" 
                           required value="{{ old('name') }}">
                    @error('name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="email@example.com" 
                           required value="{{ old('email') }}">
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" 
                               placeholder="********" required>
                        <i class="fas fa-eye-slash toggle-password" id="togglePassword"></i>
                    </div>
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password_confirmation" 
                               name="password_confirmation" placeholder="********" required>
                        <i class="fas fa-eye-slash toggle-password" id="togglePasswordConfirm"></i>
                    </div>
                    @error('password_confirmation')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Requirements -->
                <div class="password-requirements">
                    <p><i class="fas fa-info-circle"></i> <strong>Password harus:</strong></p>
                    <p><i class="fas fa-check"></i> Minimal 8 karakter</p>
                    <p><i class="fas fa-check"></i> Kombinasi huruf dan angka</p>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Pemilik Wisata
                </button>

                <!-- Cancel Button -->
                <a href="{{ route('admin.pemilik.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </form>
        </div>
    </div>

    <script>
        // Toggle Password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });

        // Toggle Password Confirmation
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirmInput.type === 'password' ? 'text' : 'password';
            passwordConfirmInput.type = type;
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    </script>
</body>
</html>