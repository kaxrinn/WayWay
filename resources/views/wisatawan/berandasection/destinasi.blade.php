<section id="destinasi" class="bg-white py-20">
    <div class="max-w-5xl mx-auto px-6">

        <!-- HEADER -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-10">

            <!-- Judul + Filter -->
            <div>
                <h2 class="text-3xl font-bold text-[#496d9e] mb-4">
                    Destinasi Populer
                </h2>

                <!-- Filter -->
                <div class="flex flex-wrap gap-4 text-sm text-[#496d9e]">
                    <button class="filter-active">Populer</button>
                    <button class="filter-item">Pantai</button>
                    <button class="filter-item">Kuliner</button>
                    <button class="filter-item">Alam</button>
                    <button class="filter-item">Budaya</button>
                </div>
            </div>

            <!-- Button kanan -->
            <div>
                <button
                    class="border border-gray-300 rounded-full px-6 py-2 text-sm text-[#496d9e] hover:bg-gray-100">
                    Lihat Semua Destinasi
                </button>
            </div>

        </div>

        <!-- CARD DESTINASI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            @for ($i = 0; $i < 4; $i++)
            <div class="rounded-3xl overflow-hidden bg-white shadow">

                <!-- Gambar -->
                <img src="images/wisata/barelang.jpg"
                    class="h-[260px] w-full object-cover"
                    alt="Destinasi">

                <!-- Text -->
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800">
                        Jembatan Barelang
                    </h3>
                    <p class="text-sm text-gray-500">
                        Batam, Kepulauan Riau
                    </p>
                </div>

            </div>
            @endfor

        </div>

    </div>
</section>
<style>
.filter-item {
    padding: 6px 14px;
    border-radius: 9999px;
    color: #6B7280;
}
.filter-item:hover {
    background: #EEEEEE;
}

.filter-active {
    padding: 6px 14px;
    border-radius: 9999px;
    background: #F4DBB4;
    color: #4E4E4E;
    font-weight: 500;
}
</style>
