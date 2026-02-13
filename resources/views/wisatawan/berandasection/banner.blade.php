<section id="beranda"
    class="relative w-full overflow-hidden
           min-h-[55vh] sm:min-h-screen
           pt-16 sm:pt-0">
    <video autoplay muted loop playsinline
        class="absolute inset-0 w-full h-full object-cover">
        <source src="{{ asset('videos/batam-hero.mp4') }}" type="video/mp4">
    </video>

    <!-- overlay biar teks kebaca -->
    <div class="absolute inset-0"></div>

    <div class="relative z-10 flex items-center justify-center 
            h-full text-center px-4 sm:px-6
            pt-10 sm:pt-25">
        <div class="text-white max-w-2xl">

            <h1 class="text-3xl sm:text-4xl md:text-6xl font-bold tracking-wide leading-tight">
                Find Your Way<br/> Enjoy the Way
            </h1>

            <p class="mt-4 sm:mt-6 text-base sm:text-lg md:text-xl text-white/90">
                AI Travel Guide for Batam
            </p>

            @auth
                <a href="/#destinasi"
                   class="inline-block mt-6 sm:mt-8 px-7 py-3 rounded-full
                          bg-[#F4DBB4] text-black text-sm font-semibold
                          hover:bg-[#f9d497] transition">
                    Temukan Destinasimu
                </a>
            @else
                <a href="{{ route('wisatawan.login') }}"
                   class="inline-block mt-6 sm:mt-8 px-7 py-3 rounded-full
                          bg-[#F4DBB4] text-black text-sm font-semibold
                          hover:bg-[#f9d497] transition">
                    Jelajahi Sekarang
                </a>
            @endauth

        </div>
    </div>
</section>
<section class="bg-white py-20 sm:px-20">
    <div class="max-w-6xl mx-auto px-6 text-center">

        <!-- Title -->
        <h2 class="text-3xl font-bold text-[#496d9e] mb-14">
            Apa yang WayWay Lakukan
        </h2>

        <!-- Features -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">

            <!-- Feature 1 -->
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 flex items-center justify-center rounded-full border-2 border-[#496d9e] mb-5">
                    <!-- Icon -->
                    <svg class="w-10 h-10 text-[#496d9e]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 20l-5.447-2.724A2 2 0 013 15.382V6.618a2 2 0 011.553-1.894L9 2m6 18l5.447-2.724A2 2 0 0021 15.382V6.618a2 2 0 00-1.553-1.894L15 2M9 2v18m6-18v18"/>
                    </svg>
                </div>

                <h3 class="font-semibold text-lg text-[#496d9e] mb-2">
                    Rekomendasi Destinasi AI
                </h3>

                <p class="text-gray-500 text-sm leading-relaxed">
                    WayWay memberikan rekomendasi destinasi, event, dan aktivitas wisata
                    berdasarkan minat, riwayat pencarian, serta ulasan pengguna secara real-time.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 flex items-center justify-center rounded-full border-2 border-[#496d9e] mb-5">
                    <svg class="w-10 h-10 text-[#496d9e]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                        <circle cx="12" cy="11" r="2"/>
                    </svg>
                </div>

                <h3 class="font-semibold text-lg text-[#496d9e] mb-2">
                    Peta Digital & Informasi Lokasi
                </h3>

                <p class="text-gray-500 text-sm leading-relaxed">
                    Integrasi peta digital untuk menampilkan lokasi destinasi,
                    rute perjalanan, serta informasi sekitar secara akurat dan mudah dipahami.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 flex items-center justify-center rounded-full border-2 border-[#496d9e] mb-5">
                    <svg class="w-10 h-10 text-[#496d9e]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M8 10h.01M12 10h.01M16 10h.01M21 16c0 1.657-1.79 3-4 3H8l-5 3V6c0-1.657 1.79-3 4-3h10c2.21 0 4 1.343 4 3v10z"/>
                    </svg>
                </div>

                <h3 class="font-semibold text-lg text-[#496d9e] mb-2">
                    Chatbot & Itinerary Otomatis
                </h3>

                <p class="text-gray-500 text-sm leading-relaxed">
                    Chatbot AI membantu menjawab pertanyaan wisatawan dan
                    menyusun itinerary perjalanan secara otomatis sesuai preferensi pengguna.
                </p>
            </div>

        </div>
    </div>
</section>
