<section id="tentang"
  class="relative w-full overflow-hidden
         min-h-[80vh] sm:min-h-screen">

  <!-- Video Background -->
  <video autoplay muted loop playsinline
    class="absolute inset-0 w-full h-full object-cover object-top">
    <source src="{{ asset('videos/abouts.mp4') }}" type="video/mp4">
  </video>

  <!-- Overlay Gelap -->
  <div class="absolute inset-0 "></div>

  <!-- CONTENT -->
  <div class="relative z-10 max-w-5xl mx-auto px-6 sm:px-10 h-full
              flex flex-col md:flex-row items-center justify-center md:justify-between gap-10">

    <!-- KIRI : Teks -->
    <div class="text-white text-center md:text-left mt-30">
      <h1 class="text-4xl
                 font-bold leading-snug mb-4"
          style="text-shadow: 4px 4px 8px rgba(0,0,0,0.6);">
        Temukan<br/>
        Destinasimu<br/>
      </h1>
<p class="text-sm font-semibold sm:text-base md:text-lg text-white/95 mb-6 sm:mb-8
          max-w-xl mx-auto lg:mx-0"
   style="text-shadow: 2px 2px 6px rgba(0,0,0,0.65);">
  Pilih destinasi terbaik sesuai minat dan kebutuhan Anda.
  Jelajahi, temukan, dan rencanakan perjalanan Anda dengan lebih mudah.
</p>`
      <a href="#"
         class="inline-block px-7 py-3 rounded-full
                bg-[#F4DBB4] text-black text-sm sm:text-base font-semibold
                hover:bg-[#f9d497] transition">
        Mulai Jelajah
      </a>
    </div>

    <!-- KANAN : PLAY -->
    <div class="flex items-center justify-center ">
      <button
        id="openVideo"
        class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24
               rounded-full bg-[#f9d497]/60 backdrop-blur-md
               flex items-center justify-center
               hover:bg-white/40 transition">
        <svg class="w-6 h-6 sm:w-8 sm:h-8 md:w-10 md:h-10 text-white ml-1"
             fill="currentColor" viewBox="0 0 24 24">
          <path d="M8 5v14l11-7z"/>
        </svg>
      </button>
    </div>
  </div>

  <!-- VIDEO MODAL -->
  <div id="videoModal"
       class="fixed inset-0 z-[999] hidden items-center justify-center
              bg-black/70 px-4">

    <div class="relative w-full max-w-4xl aspect-video
                bg-black rounded-xl overflow-hidden">

      <button
        id="closeVideo"
        class="absolute top-3 right-3 z-20
               text-white text-2xl hover:text-red-400">
        ✕
      </button>

      <div id="videoWrapper" class="w-full h-full"></div>
    </div>
  </div>
</section>
<script>
const modal = document.getElementById('videoModal');
const openBtn = document.getElementById('openVideo');
const closeBtn = document.getElementById('closeVideo');
const wrapper = document.getElementById('videoWrapper');

openBtn.onclick = () => {
  modal.classList.remove('hidden');
  modal.classList.add('flex');

  wrapper.innerHTML = `
    <iframe
      class="w-full h-full"
      src="https://www.youtube.com/embed/c43_GKscPQk?autoplay=1"
      frameborder="0"
      allow="autoplay; encrypted-media"
      allowfullscreen>
    </iframe>
  `;
};

function closeModal() {
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  wrapper.innerHTML = '';
}

closeBtn.onclick = closeModal;
modal.onclick = (e) => {
  if (e.target === modal) closeModal();
};
</script>
