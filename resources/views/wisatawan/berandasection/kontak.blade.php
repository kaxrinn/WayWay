<!-- Form Kontak -->
<section id="kontak"  class="bg-[#d4e8ef] py-20">
    <div class="max-w-6xl mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <!-- KIRI: DESKRIPSI -->
            <div>
                <h2 class="text-3xl font-bold text-[#496d9e] mb-4">
                    Butuh Bantuan atau Punya Saran?
                </h2>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Tim WayWay siap membantu perjalanan wisatamu di Kota Batam.
                    Kirimkan pertanyaan, kritik, atau saran destinasi baru agar
                    pengalaman wisata semakin nyaman dan personal.
                </p>

                <ul class="space-y-3 text-gray-700">
                    <li>Pertanyaan seputar destinasi wisata</li>
                    <li>Saran tempat wisata baru</li>
                    <li>Laporan kendala aplikasi</li>
                </ul>
            </div>

            <!-- KANAN: FORM -->
            <div class="bg-white rounded-3xl shadow-lg p-8">
                <form action="#" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama
                        </label>
                        <input type="text" placeholder="Nama kamu"
                            class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#5B9AC7] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>
                        <input type="email" placeholder="email@example.com"
                            class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#5B9AC7] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kategori
                        </label>
                        <select
                            class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#5B9AC7] focus:outline-none">
                            <option>Pertanyaan</option>
                            <option>Kritik & Saran</option>
                            <option>Saran Destinasi</option>
                            <option>Laporan Masalah</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Pesan
                        </label>
                        <textarea rows="4" placeholder="Tulis pesan kamu di sini..."
                            class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#5B9AC7] focus:outline-none"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#5B9AC7] hover:bg-[#496D9E] text-white py-3 rounded-xl transition">
                        Kirim Pesan
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

  </div>
</section>
