<section class="bg-white py-20">
    <div class="max-w-5xl mx-auto px-6">

        <!-- Judul -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-[#496d9e]">
                Event & Promosi
            </h2>
        </div>

        <!-- Horizontal Scroll -->
        <div class="flex gap-6 overflow-x-auto pb-4">

            @for ($i = 0; $i < 6; $i++)
            <div class="min-w-[320px] max-w-[320px] bg-white rounded-3xl overflow-hidden shadow">

                <!-- Poster -->
                <div class="relative">
                    <img src="images/iklan/iklan.jpg"
                        class="h-[420px] w-full object-cover"
                        alt="iklan">

                    <!-- Overlay -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent">
                    </div>

                    <!-- Badge -->
                    <span
                        class="absolute top-4 left-4 bg-[#5B9AC7] text-white text-xs px-3 py-1 rounded-full">
                        Event
                    </span>
                </div>

                <!-- Text -->
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 leading-tight">
                        Festival Bahari Batam 2026
                    </h3>
                    <p class="text-sm text-gray-500">
                        20 – 22 Juni 2026
                    </p>
                </div>
            </div>
            @endfor

        </div>

    </div>
</section>
