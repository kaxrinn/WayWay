{{-- Contact Section --}}
<section id="kontak" class="bg-white py-12 pb-28">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="text-center mb-10">
            <h2 class="text-3xl sm:text-4xl font-bold mb-2" style="color: #496d9e;">
                Contact Us
            </h2>
            <p class="text-gray-500 text-base font-medium">
                Have a question or suggestion? We're here to help
            </p>
        </div>

        {{-- Body --}}
        <div class="flex flex-col lg:flex-row items-stretch gap-0">

            {{-- KIRI: Foto gedung --}}
            <div class="relative flex-shrink-0 lg:w-72 hidden lg:block">
                <div class="h-full min-h-[460px] rounded-2xl overflow-hidden shadow-md">
                    <img src="{{ asset('images/batam building.jpg') }}"
                         alt="Batam Building"
                         class="w-full h-full object-cover">
                </div>

                {{-- Dot pattern --}}
                <div class="absolute -bottom-4 -left-3 grid gap-[3px] z-0"
                     style="grid-template-columns: repeat(10, 6px);">
                    @for($i = 0; $i < 80; $i++)
                        <span class="block w-[5px] h-[5px] rounded-full opacity-80"
                              style="background: #f5a623;"></span>
                    @endfor
                </div>
            </div>

            {{-- KANAN: Form card --}}
           <div class="flex-1 bg-white rounded-2xl shadow-2xl p-6 sm:p-8 lg:-ml-8 lg:mt-6 lg:mb-6 z-10 relative">
                {{-- ✅ SUCCESS ALERT --}}
                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl text-sm mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('hubungi.kami.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Name*</label>
                        <input type="text" name="nama" required value="{{ old('nama') }}"
                               placeholder="Enter your name"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl
                                      bg-[#fefaf6] focus:outline-none focus:ring-2 focus:ring-[#5B9AC7]
                                      transition placeholder-gray-300">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email*</label>
                        <input type="email" name="email" required value="{{ old('email') }}"
                               placeholder="Enter your email"
                               class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl
                                      bg-[#fdf8f2] focus:outline-none focus:ring-2 focus:ring-[#5B9AC7]
                                      transition placeholder-gray-300">
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Category*</label>
                        <select name="subjek" required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl
                                       bg-[#fdf8f2] focus:outline-none focus:ring-2 focus:ring-[#5B9AC7]
                                       transition appearance-none">
                            <option value="">Select a category</option>
                            <option value="pertanyaan" {{ old('subjek') == 'pertanyaan' ? 'selected' : '' }}>General Question</option>
                            <option value="kritik" {{ old('subjek') == 'kritik' ? 'selected' : '' }}>Feedback & Suggestions</option>
                            <option value="destinasi" {{ old('subjek') == 'destinasi' ? 'selected' : '' }}>New Destination Suggestion</option>
                            <option value="laporan" {{ old('subjek') == 'laporan' ? 'selected' : '' }}>Report an Issue</option>
                        </select>
                    </div>

                    {{-- Message --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Message*</label>
                        <textarea name="pesan" rows="4" required
                                  placeholder="Enter your message"
                                  class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl
                                         bg-[#fdf8f2] focus:outline-none focus:ring-2 focus:ring-[#5B9AC7]
                                         transition resize-none placeholder-gray-300">{{ old('pesan') }}</textarea>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full py-3 rounded-xl text-sm font-semibold transition hover:opacity-90"
                            style="background: #F5DBB4; color: #7a5a30;">
                        Send Message
                    </button>

                </form>
            </div>

        </div>
    </div>
</section>