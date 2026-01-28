<section id="tentang" class="relative h-screen w-full overflow-hidden">

  <!-- Video Background -->
  <video autoplay muted loop playsinline
    class="absolute inset-0 w-full h-full object-cover object-top">
    <source src="{{ asset('videos/abouts.mp4') }}" type="video/mp4">
  </video>

  <!-- Overlay -->
  <div class="absolute inset-0 "></div>

  <!-- CONTENT -->
  <div class="relative z-10 max-w-6xl mx-auto px-6 h-full
              flex flex-col md:flex-row items-center justify-between gap-14">

    <!-- KIRI : Teks -->
    <div class="text-white max-w-xl">
      <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6"
          style="text-shadow: 5px 5px 8px rgba(0,0,0,0.5);">
        Siap Berwisata Dengan<br/>
        Petualangan Nyata dan<br/>
        Menikmati Alam
      </h1>


      <a href="#"
         class="inline-block mt-8 px-8 py-3 rounded-full
                bg-[#F4DBB4] text-black text-sm font-semibold
                hover:bg-[#f9d497] transition">
        Mulai Jelajah
      </a>
    </div>

    <!-- KANAN : PLAY -->
    <div class="flex items-center justify-center">
      <a href="https://www.youtube.com/watch?v=c43_GKscPQk"
         class="w-24 h-24 rounded-full bg-[#f9d497]/50 backdrop-blur-md
                flex items-center justify-center
                hover:bg-white/35 transition">
        <svg class="w-10 h-10 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
          <path d="M8 5v14l11-7z"/>
        </svg>
      </a>
    </div>

  </div>
</section>
