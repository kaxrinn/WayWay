<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner with Us — WayWay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-overlay {
            background: linear-gradient(135deg, rgba(80, 141, 177, 0.7), rgba(167, 191, 228, 0.6)),
                        url("{{ asset('Background/auth.jpg') }}") center/cover fixed;
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
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
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
<body class="bg-overlay min-h-screen overflow-x-hidden flex items-center justify-center relative px-4 py-10">

    <div class="grid lg:grid-cols-[1.2fr_1fr] grid-cols-1 max-w-[1100px] w-full lg:w-[90%] max-w-md lg:max-w-[1100px] lg:min-h-[550px] relative z-10 gap-0">

        <!-- Left Section -->
        <div class="hidden lg:flex flex-col justify-center text-white p-10 lg:p-12">
            <div class="flex items-center gap-3 mb-3 animate-fadeInDown">
                <img src="{{ asset('assets/Logo/logoo.png') }}" alt="Logo" class="h-10">
                <span class="text-2xl font-bold tracking-[2px]">WAYWAY</span>
            </div>
            <h1 class="text-5xl lg:text-[56px] font-extrabold leading-tight mb-5 drop-shadow-lg animate-fadeInUp">
                GROW<br>WITH WAYWAY
            </h1>
            <p class="text-xl font-light mb-2 opacity-95 animate-fadeInUp" style="animation-delay: 0.1s;">
                Partner with us as a destination owner or travel agent and reach more travelers.
            </p>
            <p class="text-sm opacity-85 leading-relaxed animate-fadeInUp" style="animation-delay: 0.2s;">
                Fill in the form, and our admin team<br>will review and contact you by email.
            </p>
        </div>

        <!-- Right Section -->
        <div class="bg-white/15 backdrop-blur-[30px] border border-white/20 rounded-3xl p-8 lg:p-12 flex flex-col justify-center shadow-2xl lg:ml-20">

            <!-- Logo for Mobile Only -->
            <div class="flex justify-center mb-6 lg:hidden">
                <img src="{{ asset('assets/Logo/logodnnama.png') }}" alt="Logo" class="h-20">
            </div>

            <p class="text-sm font-medium text-white mb-5 text-center tracking-wide opacity-90">
                Partner Registration Form
            </p>

            {{-- Success notification --}}
            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded-xl bg-green-500/20 border border-green-400/40 text-white text-sm text-center animate-fadeIn">
                    <i class="fas fa-circle-check mr-1"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('mitra.submit') }}" id="partnerForm">
                @csrf

                <!-- Partner Type -->
                <div class="mb-4">
                    <label class="block text-[13px] font-medium text-white mb-2 opacity-90">I am a</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-center gap-2 px-4 py-3 bg-white/90 border border-white/30 rounded-xl text-sm text-gray-800 cursor-pointer transition-all duration-300 hover:bg-white">
                            <input type="radio" name="partner_type" value="Destination Owner" required {{ old('partner_type') == 'Destination Owner' ? 'checked' : '' }} class="accent-[#9eccdb]">
                            Destination Owner
                        </label>
                        <label class="flex items-center justify-center gap-2 px-4 py-3 bg-white/90 border border-white/30 rounded-xl text-sm text-gray-800 cursor-pointer transition-all duration-300 hover:bg-white">
                            <input type="radio" name="partner_type" value="Travel Agent" required {{ old('partner_type') == 'Travel Agent' ? 'checked' : '' }} class="accent-[#9eccdb]">
                            Travel Agent
                        </label>
                    </div>
                    @error('partner_type')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Full Name -->
                <div class="mb-4">
                    <label for="name" class="block text-[13px] font-medium text-white mb-2 opacity-90">Full Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Full Name"
                        required
                        value="{{ old('name') }}"
                        class="w-full px-5 py-3 bg-white/90 border border-white/30 rounded-xl outline-none text-sm text-gray-800 placeholder-gray-400 transition-all duration-300 focus:bg-white focus:border-blue-400/50 focus:shadow-[0_0_0_3px_rgba(76,175,80,0.1)] focus:-translate-y-0.5">
                    @error('name')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Business Name -->
                <div class="mb-4">
                    <label for="business_name" class="block text-[13px] font-medium text-white mb-2 opacity-90">Business / Agency Name</label>
                    <input
                        type="text"
                        id="business_name"
                        name="business_name"
                        placeholder="e.g. Batam Adventure Tours"
                        required
                        value="{{ old('business_name') }}"
                        class="w-full px-5 py-3 bg-white/90 border border-white/30 rounded-xl outline-none text-sm text-gray-800 placeholder-gray-400 transition-all duration-300 focus:bg-white focus:border-blue-400/50 focus:shadow-[0_0_0_3px_rgba(76,175,80,0.1)] focus:-translate-y-0.5">
                    @error('business_name')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-[13px] font-medium text-white mb-2 opacity-90">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="email@example.com"
                        required
                        value="{{ old('email') }}"
                        class="w-full px-5 py-3 bg-white/90 border border-white/30 rounded-xl outline-none text-sm text-gray-800 placeholder-gray-400 transition-all duration-300 focus:bg-white focus:border-blue-400/50 focus:shadow-[0_0_0_3px_rgba(76,175,80,0.1)] focus:-translate-y-0.5">
                    @error('email')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- WhatsApp Number -->
                <div class="mb-4">
                    <label for="whatsapp" class="block text-[13px] font-medium text-white mb-2 opacity-90">WhatsApp Number</label>
                    <input
                        type="text"
                        id="whatsapp"
                        name="whatsapp"
                        placeholder="e.g. 08123456789"
                        required
                        value="{{ old('whatsapp') }}"
                        class="w-full px-5 py-3 bg-white/90 border border-white/30 rounded-xl outline-none text-sm text-gray-800 placeholder-gray-400 transition-all duration-300 focus:bg-white focus:border-blue-400/50 focus:shadow-[0_0_0_3px_rgba(76,175,80,0.1)] focus:-translate-y-0.5">
                    @error('whatsapp')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Business Description -->
                <div class="mb-4">
                    <label for="description" class="block text-[13px] font-medium text-white mb-2 opacity-90">Tell Us About Your Business</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Briefly describe your destination, tour packages, or services..."
                        required
                        class="w-full px-5 py-3 bg-white/90 border border-white/30 rounded-xl outline-none text-sm text-gray-800 placeholder-gray-400 transition-all duration-300 focus:bg-white focus:border-blue-400/50 focus:shadow-[0_0_0_3px_rgba(76,175,80,0.1)] focus:-translate-y-0.5 resize-none">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Website / Social Media (optional) -->
                <div class="mb-4">
                    <label for="link" class="block text-[13px] font-medium text-white mb-2 opacity-90">Website / Instagram <span class="opacity-60">(optional)</span></label>
                    <input
                        type="text"
                        id="link"
                        name="link"
                        placeholder="https:// or @username"
                        value="{{ old('link') }}"
                        class="w-full px-5 py-3 bg-white/90 border border-white/30 rounded-xl outline-none text-sm text-gray-800 placeholder-gray-400 transition-all duration-300 focus:bg-white focus:border-blue-400/50 focus:shadow-[0_0_0_3px_rgba(76,175,80,0.1)] focus:-translate-y-0.5">
                    @error('link')
                        <span class="text-red-400 text-[11px] mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full py-3.5 bg-brand hover:bg-[#8abecf] border-none rounded-xl text-white text-sm font-semibold tracking-wider cursor-pointer transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5 mt-2">
                    SUBMIT APPLICATION
                </button>
            </form>

            <!-- Back to Home Link -->
            <div class="text-center mt-5 text-[13px] text-white">
                <a href="{{ route('wisatawan.beranda') }}" class="text-white font-semibold border-b border-white hover:opacity-80 transition-opacity no-underline">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

</body>
</html>