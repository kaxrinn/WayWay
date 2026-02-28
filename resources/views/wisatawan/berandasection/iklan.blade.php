@if(isset($iklanAktif) && $iklanAktif->count() > 0)

<style>
.iklan-dot {
    width: 10px;
    height: 10px;
    border-radius: 9999px;
    background: #d1d5db;
    cursor: pointer;
    transition: all 0.3s ease;
}
.iklan-dot-active {
    width: 24px;
    background: #496d9e;
}

/* Hide scrollbar */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

<section class="bg-white py-20">
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8 sm:px-5">
        <h2 class="text-3xl font-bold text-[#496d9e]">
            Special Offers
        </h2>
    </div>

    <!-- WRAPPER -->
    <div class="relative">

        <!-- ARROW - Hidden on mobile -->
        <button id="iklanScrollLeft"
            class="hidden lg:flex absolute -left-15 top-1/2 -translate-y-1/2 z-20
                   w-11 h-11 rounded-full bg-white shadow-lg items-center justify-center
                   text-gray-700 text-2xl font-light
                   hover:bg-[#496d9e] hover:text-white hover:shadow-xl transition-all">
            ‹
        </button>

        <button id="iklanScrollRight"
            class="hidden lg:flex absolute -right-15 top-1/2 -translate-y-1/2 z-20
                   w-11 h-11 rounded-full bg-white shadow-lg items-center justify-center
                   text-gray-700 text-2xl font-light
                   hover:bg-[#496d9e] hover:text-white hover:shadow-xl transition-all">
            ›
        </button>

        <!-- TRACK -->
        <div class="overflow-x-auto lg:overflow-hidden scrollbar-hide px-4 sm:px-6 lg:px-0">
            <div id="iklanTrack"
                 class="flex gap-4 sm:gap-5 lg:gap-6 transition-transform duration-500 ease-in-out">
                
                @foreach($iklanAktif as $iklan)
                <div class="iklan-card flex-shrink-0 w-full sm:w-[300px] lg:w-[340px]
                            bg-white rounded-[24px]
                            shadow-[0_2px_16px_rgba(0,0,0,0.08)] hover:shadow-[0_8px_24px_rgba(0,0,0,0.15)]
                            transition-all duration-300 overflow-hidden group cursor-pointer"
                     onclick="openPopup(
                        '{{ Storage::url($iklan->banner_promosi) }}',
                        '{{ addslashes($iklan->judul_banner) }}',
                        '{{ addslashes($iklan->deskripsi_banner ?? '') }}',
                        '{{ $iklan->tanggal_selesai->format('d M Y') }}'
                     )">

                    <!-- IMAGE CONTAINER -->
                    <div class="relative h-[200px] sm:h-[180px] overflow-hidden">
                        <img src="{{ Storage::url($iklan->banner_promosi) }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500 rounded-t-[24px]">
                    </div>

                    <!-- CONTENT -->
                    <div class="p-4 sm:p-5">
                        <!-- TITLE -->
                        <h3 class="font-bold text-gray-900 text-base sm:text-lg mb-1.5 leading-tight line-clamp-2">
                            {{ $iklan->judul_banner }}
                        </h3>

                        <!-- DESCRIPTION -->
                        <p class="text-xs sm:text-sm text-gray-500 leading-relaxed mb-4 line-clamp-2 min-h-[36px]">
                            {{ $iklan->deskripsi_banner }}
                        </p>

                        <!-- FOOTER -->
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <span class="text-xs text-gray-400">
                                
                            </span>
                            <button class="px-4 sm:px-5 py-2 bg-[#1a3b5d] hover:bg-[#0f2942] 
                                          text-white text-xs sm:text-sm font-semibold rounded-full
                                          transition-all duration-300">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>

    <!-- DOT -->
    <div id="iklanDots" class="flex justify-center gap-2 mt-6"></div>

</div>
</section>

<!-- POPUP - Image + Information -->
<div id="popupModal"
     class="hidden fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-4"
     onclick="closePopup()">

    <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col"
         onclick="event.stopPropagation()">

        <!-- Close Button -->
        <button onclick="closePopup()"
                class="absolute top-4 right-4 bg-white/90 hover:bg-white shadow-xl 
                       w-10 h-10 rounded-full flex items-center justify-center z-10
                       transition-all duration-300 hover:scale-110">
            <span class="text-gray-700 text-xl font-light">✕</span>
        </button>

        <!-- Image Container -->
        <div class="w-full bg-gray-100 flex items-center justify-center overflow-hidden">
            <img id="popupImage" 
                 class="w-full max-h-[50vh] object-contain">
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto">
            <h3 id="popupTitle" 
                class="text-2xl font-bold text-gray-900 mb-3"></h3>
            
            <p id="popupDesc" 
               class="text-gray-600 leading-relaxed mb-4"></p>
            
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

let currentSlide = 0;
let slidesToShow = 3;

const track = document.getElementById('iklanTrack');
const cards = Array.from(document.querySelectorAll('.iklan-card'));
const prevBtn = document.getElementById('iklanScrollLeft');
const nextBtn = document.getElementById('iklanScrollRight');
const dotsContainer = document.getElementById('iklanDots');

function updateSlidesToShow() {
    if (window.innerWidth < 640) {
        slidesToShow = 1;
        track.parentElement.style.overflowX = 'auto';
    } else if (window.innerWidth < 1024) {
        slidesToShow = 2;
        track.parentElement.style.overflowX = 'auto';
    } else {
        slidesToShow = 3;
        track.parentElement.style.overflowX = 'hidden';
    }
}

function updateSlider() {
    if (!cards.length) return;

    const cardWidth = cards[0].offsetWidth + 24;
    track.style.transform = `translateX(-${currentSlide * cardWidth}px)`;

    [...dotsContainer.children].forEach((dot, i) => {
        dot.classList.toggle('iklan-dot-active', i === currentSlide);
    });
}

function createDots() {
    dotsContainer.innerHTML = '';
    const maxSlide = Math.max(0, cards.length - slidesToShow);

    for (let i = 0; i <= maxSlide; i++) {
        const dot = document.createElement('div');
        dot.className = 'iklan-dot' + (i === 0 ? ' iklan-dot-active' : '');
        dot.onclick = () => {
            currentSlide = i;
            updateSlider();
        };
        dotsContainer.appendChild(dot);
    }
}

prevBtn?.addEventListener('click', () => {
    currentSlide = Math.max(0, currentSlide - 1);
    updateSlider();
});

nextBtn?.addEventListener('click', () => {
    const maxSlide = cards.length - slidesToShow;
    currentSlide = Math.min(maxSlide, currentSlide + 1);
    updateSlider();
});

window.addEventListener('resize', () => {
    updateSlidesToShow();
    currentSlide = 0;
    createDots();
    updateSlider();
});

updateSlidesToShow();
createDots();
updateSlider();

});

// POPUP FUNCTIONS
function openPopup(image, title, desc, date) {
    document.getElementById('popupImage').src = image;
    document.getElementById('popupTitle').innerText = title;
    document.getElementById('popupDesc').innerText = desc;
    
    document.getElementById('popupModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePopup() {
    document.getElementById('popupModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePopup();
    }
});
</script>

@endif