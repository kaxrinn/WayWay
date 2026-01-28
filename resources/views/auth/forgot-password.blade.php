<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi</title>
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
<body class="bg-overlay min-h-screen flex items-center justify-center relative px-4 py-10">
    
    <div class="w-full max-w-md relative z-10 animate-fadeInUp">

        <!-- Form Card -->
        <div class="bg-white/15 backdrop-blur-[30px] border border-white/20 rounded-3xl p-8 lg:p-12 shadow-2xl">
            
            <!-- Logo Inside Box -->
            <div class="flex justify-center mb-6">
                <img src="{{ asset('assets/Logo/logodnnama.png') }}" alt="Logo" class="h-20">
            </div>
            
            <h2 class="text-2xl font-bold text-white text-center mb-3">Lupa Kata Sandi?</h2>
            <p class="text-sm text-white/90 text-center mb-8 leading-relaxed">
                Jangan khawatir! Masukkan email Anda dan kami akan mengirimkan panduan untuk mengatur ulang kata sandi.
            </p>

            <form method="POST" action="{{ route('wisatawan.password.email') }}">
                @csrf
                
                <!-- Email Input -->
                <div class="mb-5">
                    <label for="email" class="block text-[13px] font-medium text-white mb-2 opacity-90">Email</label>
                    <div class="relative">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="email@example.com" 
                            required
                            class="w-full px-5 py-3.5 bg-white/90 border border-white/30 rounded-xl outline-none text-sm text-gray-800 placeholder-gray-400 transition-all duration-300 focus:bg-white focus:border-blue-400/50 focus:shadow-[0_0_0_3px_rgba(76,175,80,0.1)] focus:-translate-y-0.5">
                    </div>
                    @error('email')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                    @if(session('success'))
                        <span class="text-green-400 text-[11px] mt-1 block">{{ session('success') }}</span>
                    @endif
                </div>

                <!-- Submit Button with Brand Color -->
                <button 
                    type="submit" 
                    class="w-full py-3.5 bg-brand hover:bg-[#8abecf] border-none rounded-xl text-white text-sm font-semibold tracking-wider cursor-pointer transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                    KIRIM LINK ATUR ULANG
                </button>
            </form>

            <!-- Back Link -->
            <div class="text-center mt-6">
                <a href="{{ route('wisatawan.login') }}" 
                   class="inline-flex items-center gap-2 text-sm text-white font-semibold border-b border-white hover:opacity-80 transition-opacity no-underline">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Kembali ke Halaman Masuk
                </a>
            </div>

        </div>
    </div>

</body>
</html>