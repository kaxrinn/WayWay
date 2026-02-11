@if(isset($iklanAktif) && $iklanAktif->count() > 0)

<section class="bg-white py-10 px-4">
    <div class="max-w-5xl mx-auto">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl md:text-3xl font-bold text-[#496d9e]">
                Penawaran Spesial
            </h2>

            <div class="flex gap-2">
                <button onclick="slidePrev()" 
                        class="w-9 h-9 bg-[#496d9e] text-white rounded-full">
                    ←
                </button>
                <button onclick="slideNext()" 
                        class="w-9 h-9 bg-[#496d9e] text-white rounded-full">
                    →
                </button>
            </div>
        </div>

        <!-- Slider -->
        <div class="overflow-hidden">
            <div id="sliderTrack"
                 class="flex transition-transform duration-500 ease-in-out">

                @foreach($iklanAktif as $iklan)
                <div class="w-full sm:w-1/2 lg:w-1/3 flex-shrink-0 p-3">

                    <div onclick="openPopup(
                        '{{ Storage::url($iklan->banner_promosi) }}',
                        '{{ addslashes($iklan->judul_banner) }}',
                        '{{ addslashes($iklan->deskripsi_banner ?? '') }}',
                        '{{ $iklan->tanggal_selesai->format('d M Y') }}'
                    )"
                    class="bg-white rounded-2xl shadow hover:shadow-lg 
                           transition cursor-pointer 
                           h-[420px] flex flex-col overflow-hidden">

                        <img src="{{ Storage::url($iklan->banner_promosi) }}"
                             class="h-56 w-full object-cover">

                        <div class="p-4 flex flex-col flex-1">

                            <h3 class="font-bold text-lg line-clamp-2">
                                {{ $iklan->judul_banner }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-2 line-clamp-3">
                                {{ $iklan->deskripsi_banner }}
                            </p>

                            <div class="mt-auto">
                                <p class="text-xs text-gray-400 mt-4">
                                    Hingga {{ $iklan->tanggal_selesai->format('d M Y') }}
                                </p>
                            </div>

                        </div>

                    </div>
                </div>
                @endforeach

            </div>
        </div>

    </div>
</section>

<!-- POPUP -->
<div id="popupModal"
     class="hidden fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">

    <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden relative">

        <button onclick="closePopup()"
                class="absolute top-4 right-4 bg-white shadow px-3 py-1 rounded-full">
            ✕
        </button>

        <img id="popupImage" class="w-full max-h-96 object-cover">

        <div class="p-6">
            <h3 id="popupTitle" class="text-xl md:text-2xl font-bold mb-3"></h3>
            <p id="popupDesc" class="text-gray-600 mb-4 text-sm md:text-base"></p>
            <p class="text-sm text-gray-400">
                Berlaku hingga <span id="popupDate"></span>
            </p>
        </div>

    </div>
</div>

<script>
let currentIndex = 0;
const totalSlides = {{ $iklanAktif->count() }};

function getVisibleSlides() {
    if (window.innerWidth < 640) return 1;
    if (window.innerWidth < 1024) return 2;
    return 3;
}

function updateSlider() {
    const track = document.getElementById('sliderTrack');
    const slideWidth = track.children[0].offsetWidth;
    track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
}

function slideNext() {
    const visible = getVisibleSlides();
    if (currentIndex < totalSlides - visible) {
        currentIndex++;
        updateSlider();
    }
}

function slidePrev() {
    if (currentIndex > 0) {
        currentIndex--;
        updateSlider();
    }
}

window.addEventListener('resize', function() {
    currentIndex = 0;
    updateSlider();
});

function openPopup(image, title, desc, date) {
    document.getElementById('popupImage').src = image;
    document.getElementById('popupTitle').innerText = title;
    document.getElementById('popupDesc').innerText = desc;
    document.getElementById('popupDate').innerText = date;

    document.getElementById('popupModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePopup() {
    document.getElementById('popupModal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.getElementById('popupModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePopup();
    }
});
</script>

@endif
