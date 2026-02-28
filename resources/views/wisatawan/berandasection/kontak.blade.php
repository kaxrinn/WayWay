<!-- Contact Section -->
<section id="kontak" class="bg-[#d4e8ef] pb-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-[#496d9e] mb-3">
                Contact Us
            </h2>
            <p class="text-gray-600 text-lg">
                Have a question or suggestion? We're here to help
            </p>
        </div>

        <div class="grid lg:grid-cols-5 gap-8 lg:gap-12">

            <!-- LEFT: Info Cards - 2 columns -->
            <div class="lg:col-span-2">
                <div class="grid grid-cols-2 gap-4">
                    
                    <div class="bg-slate-50 rounded-2xl p-5 hover:bg-slate-100 transition">
                        <div class="w-10 h-10 bg-[#496d9e]/10 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-[#496d9e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900 text-sm mb-1">Questions</h4>
                        <p class="text-xs text-gray-600">Ask about destinations</p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-5 hover:bg-slate-100 transition">
                        <div class="w-10 h-10 bg-[#496d9e]/10 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-[#496d9e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900 text-sm mb-1">Feedback</h4>
                        <p class="text-xs text-gray-600">Help us improve</p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-5 hover:bg-slate-100 transition">
                        <div class="w-10 h-10 bg-[#496d9e]/10 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-[#496d9e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900 text-sm mb-1">Suggest a Place</h4>
                        <p class="text-xs text-gray-600">Recommend a new destination</p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-5 hover:bg-slate-100 transition">
                        <div class="w-10 h-10 bg-[#496d9e]/10 rounded-lg flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-[#496d9e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h4 class="font-semibold text-gray-900 text-sm mb-1">Report</h4>
                        <p class="text-xs text-gray-600">Report a technical issue</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Form - 3 columns -->
            <div class="lg:col-span-3">
                <div class="bg-slate-50 rounded-2xl p-6 sm:p-8">
                    
                    <form action="{{ route('hubungi.kami.store') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Name
                            </label>
                            <input type="text" name="nama" required
                                   placeholder="Full name"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg 
                                          focus:ring-2 focus:ring-[#496d9e] focus:border-[#496d9e]
                                          bg-white transition">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" name="email" required
                                   placeholder="email@example.com"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg 
                                          focus:ring-2 focus:ring-[#496d9e] focus:border-[#496d9e]
                                          bg-white transition">
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Category
                            </label>
                            <select name="subjek" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg 
                                           focus:ring-2 focus:ring-[#496d9e] focus:border-[#496d9e]
                                           bg-white transition">
                                <option value="">Select a category</option>
                                <option value="pertanyaan">General Question</option>
                                <option value="kritik">Feedback & Suggestions</option>
                                <option value="destinasi">New Destination Suggestion</option>
                                <option value="laporan">Report an Issue</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Message
                            </label>
                            <textarea name="pesan" rows="4" required
                                      placeholder="Write your message here..."
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg 
                                             focus:ring-2 focus:ring-[#496d9e] focus:border-[#496d9e]
                                             bg-white transition resize-none"></textarea>
                        </div>

                        <!-- Submit -->
                        <button type="submit"
                                class="w-full bg-[#496d9e] hover:bg-[#3d5a7f]
                                       text-white font-medium py-3 rounded-lg 
                                       transition">
                            Send Message
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</section>