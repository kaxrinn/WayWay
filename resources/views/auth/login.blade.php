<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Guide ME</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-overlay {
            background: linear-gradient(135deg, rgba(55, 87, 175, 0.7), rgba(167, 191, 228, 0.6)),
                        url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920&q=80') center/cover fixed;
        }
        .bg-overlay::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, rgba(76, 175, 80, 0.1) 0%, rgba(33, 150, 243, 0.1) 100%);
            animation: gradientShift 15s ease infinite;
        }
        @keyframes gradientShift {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fadeInDown { animation: fadeInDown 0.8s ease; }
        .animate-fadeInUp { animation: fadeInUp 0.8s ease; }
        .animate-fadeIn { animation: fadeIn 1s ease; }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#9eccdb',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-overlay h-screen overflow-hidden flex items-center justify-center relative px-4">
    
    <div class="grid lg:grid-cols-[1.2fr_1fr] grid-cols-1 max-w-[1100px] w-full lg:w-[90%] max-w-md lg:max-w-[1100px] lg:min-h-[550px] relative z-10 gap-0">
        
        <!-- Left Section -->
        <div class="hidden lg:flex flex-col justify-center text-white p-10 lg:p-12">
            <div class="flex items-center gap-3 mb-3 animate-fadeInDown">
                <img src="{{ asset('assets/Logo/logoo.png') }}" alt="Logo" class="h-10">
                <span class="text-2xl font-bold tracking-[2px]">WAYWAY</span>
            </div>
            <h1 class="text-5xl lg:text-[56px] font-extrabold leading-tight mb-5 drop-shadow-lg animate-fadeInUp">
                TEMUKAN<br>DESTINASIMU
            </h1>
            <p class="text-xl font-light mb-2 opacity-95 animate-fadeInUp" style="animation-delay: 0.1s;">
                Pilih destinasi terbaik sesuai minat<br>dan kebutuhan Anda.
            </p>
            <p class="text-sm opacity-85 leading-relaxed animate-fadeInUp" style="animation-delay: 0.2s;">
                Jelajahi, temukan, <br>dan rencanakan perjalanan Anda dengan lebih mudah.
            </p>
        </div>

        <!-- Right Section -->
        <div class="bg-white/15 backdrop-blur-[30px] border border-white/20 rounded-3xl p-8 lg:p-12 flex flex-col justify-center shadow-2xl lg:ml-20">
            
            <!-- Logo for Mobile Only -->
            <div class="flex justify-center mb-6 lg:hidden">
                <img src="{{ asset('assets/Logo/logodnnama.png') }}" alt="Logo" class="h-20">
            </div>

            <p class="text-sm font-medium text-white mb-5 text-center tracking-wide opacity-90">
                Selamat Datang, Silahkan Masuk!
            </p>

            <form method="POST" action="{{ route('wisatawan.loginPost') }}">
                @csrf
                
                <!-- Email Input -->
                <div class="mb-5">
                    <label for="email" class="block text-[13px] font-medium text-white mb-2 opacity-90">Email</label>
                    <div class="relative">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email" 
                            required 
                            value="{{ old('email') }}"
                            class="w-full px-5 py-3.5 bg-white/90 border border-white/30 rounded-xl outline-none text-sm text-gray-800 placeholder-gray-400 transition-all duration-300 focus:bg-white focus:border-blue-400/50 focus:shadow-[0_0_0_3px_rgba(76,175,80,0.1)] focus:-translate-y-0.5">
                    </div>
                    @error('email')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-5">
                    <label for="password" class="block text-[13px] font-medium text-white mb-2 opacity-90">Kata Sandi</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Enter your password" 
                            required
                            class="w-full px-5 py-3.5 bg-white/90 border border-white/30 rounded-xl outline-none text-sm text-gray-800 placeholder-gray-400 transition-all duration-300 focus:bg-white focus:border-blue-400/50 focus:shadow-[0_0_0_3px_rgba(76,175,80,0.1)] focus:-translate-y-0.5">
                        <i class="fas fa-eye-slash absolute right-4 top-1/2 -translate-y-1/2 cursor-pointer text-gray-600 hover:text-brand transition-colors" id="togglePassword"></i>
                    </div>
                    @error('password')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Forgot Password Link -->
                <div class="text-right mb-5">
                    <a href="{{ route('wisatawan.password.request') }}" class="text-xs text-white opacity-80 hover:opacity-100 transition-opacity">
                        Lupa Kata Sandi?
                    </a>
                </div>

                <!-- Submit Button with Brand Color -->
                <button 
                    type="submit" 
                    class="w-full py-3.5 bg-brand hover:bg-[#8abecf] border-none rounded-xl text-white text-sm font-semibold tracking-wider cursor-pointer transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    MASUK
                </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center my-5 text-white text-xs opacity-70">
                <div class="flex-1 h-px bg-white/30"></div>
                <span class="px-4">ATAU</span>
                <div class="flex-1 h-px bg-white/30"></div>
            </div>

            <!-- Google Login Button -->
            <a 
                href="{{ route('auth.google') }}" 
                class="w-full py-3.5 bg-white/90 border border-white/30 rounded-xl flex items-center justify-center gap-2.5 cursor-pointer transition-all duration-300 text-sm font-medium text-gray-800 hover:bg-white hover:-translate-y-0.5 hover:shadow-lg no-underline">
                <img src="https://www.google.com/images/branding/googleg/1x/googleg_standard_color_128dp.png" alt="Google" class="w-[18px] h-[18px]">
                Masuk dengan Google
            </a>

            <!-- Register Link -->
            <div class="text-center mt-5 text-[13px] text-white">
                Belum Punya Akun? 
                <a href="{{ route('wisatawan.register') }}" class="text-white font-semibold border-b border-white hover:opacity-80 transition-opacity no-underline">
                    Buat Akun
                </a>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    </script>
</body>

@if(session('success'))
<div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black/40 z-50">
    <div class="bg-white rounded-2xl p-6 w-[350px] text-center border-t-8" style="border-color:#9eccdb;">
        <h2 class="text-lg font-bold mb-2" style="color:#9eccdb;">Registrasi Berhasil</h2>
        <p class="text-sm text-gray-600 mb-4">{{ session('success') }}</p>
        <button onclick="closeModal()" class="px-4 py-2 rounded-lg text-white" style="background:#9eccdb;">
            OK
        </button>
    </div>
</div>

<script>
function closeModal(){
    document.getElementById('successModal').style.display = 'none';
}
</script>
@endif

</html>