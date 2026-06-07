@extends('layouts.travel-agent')

@section('title', 'Contact Admin')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-6">
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-phone-alt"></i>
                Contact WayWay Admin
            </h1>
            <p class="text-green-100 mt-2">We're ready to help your business grow</p>
        </div>

        <!-- Contact Info -->
        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- WhatsApp -->
                <a href="https://wa.me/6289520428618?text=Halo%20Admin%20WayWay%20-%20Saya%20ingin%20bertanya%20tentang%20paket%20subscription" 
                   target="_blank"
                   class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg text-white p-6 hover:shadow-lg transition block">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <i class="fas fa-whatsapp text-5xl"></i>
                        </div>
                        <i class="fas fa-arrow-right text-2xl opacity-50"></i>
                    </div>
                    <h3 class="text-lg font-bold">WhatsApp</h3>
                    <p class="text-green-100 text-sm mt-2">+62 895 2042 8618</p>
                    <p class="text-green-100 text-xs mt-3">Fast response, 24/7 (Mon-Fri 08:00-17:00 WIB)</p>
                </a>

                <!-- Email -->
                <a href="mailto:waywaypolibatam@gmail.com?subject=Pertanyaan%20Paket%20Subscription&body=Halo%20Admin%20WayWay"
                   class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg text-white p-6 hover:shadow-lg transition block">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <i class="fas fa-envelope text-5xl"></i>
                        </div>
                        <i class="fas fa-arrow-right text-2xl opacity-50"></i>
                    </div>
                    <h3 class="text-lg font-bold">Email</h3>
                    <p class="text-blue-100 text-sm mt-2">waywaypolibatam@gmail.com</p>
                    <p class="text-blue-100 text-xs mt-3">Reply within 1-2 business hours</p>
                </a>

                <!-- Instagram -->
                <a href="https://instagram.com/wayway" 
                   target="_blank"
                   class="bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg text-white p-6 hover:shadow-lg transition block">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <i class="fas fa-instagram text-5xl"></i>
                        </div>
                        <i class="fas fa-arrow-right text-2xl opacity-50"></i>
                    </div>
                    <h3 class="text-lg font-bold">Instagram DM</h3>
                    <p class="text-pink-100 text-sm mt-2">@wayway</p>
                    <p class="text-pink-100 text-xs mt-3">Follow and DM us for the latest updates</p>
                </a>

                <!-- Phone -->
                <a href="tel:+62812345678"
                   class="bg-gradient-to-br from-orange-500 to-red-600 rounded-lg text-white p-6 hover:shadow-lg transition block">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <i class="fas fa-phone text-5xl"></i>
                        </div>
                        <i class="fas fa-arrow-right text-2xl opacity-50"></i>
                    </div>
                    <h3 class="text-lg font-bold">Phone</h3>
                    <p class="text-orange-100 text-sm mt-2">+62 895 2042 8618</p>
                    <p class="text-orange-100 text-xs mt-3">Mon-Fri 08:00-17:00 WIB</p>
                </a>
            </div>

            <!-- FAQ Section -->
            <div class="border-t pt-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Frequently Asked Questions</h2>
                
                <div class="space-y-4">
                    <details class="border border-gray-300 rounded-lg p-4 cursor-pointer group">
                        <summary class="font-bold text-gray-800 flex justify-between items-center">
                            How do I upgrade my package?
                            <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                        </summary>
                        <p class="text-gray-600 mt-4">
                            You can upgrade your package through the "Upgrade Package" menu on the dashboard. 
                            Choose the desired package, then complete the payment via Midtrans. 
                            The new package will be activated after payment is confirmed.
                        </p>
                    </details>

                    <details class="border border-gray-300 rounded-lg p-4 cursor-pointer group">
                        <summary class="font-bold text-gray-800 flex justify-between items-center">
                            Can I cancel my package?
                            <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                        </summary>
                        <p class="text-gray-600 mt-4">
                            Packages cannot be cancelled, but your package will remain valid for its duration. 
                            You can upgrade to another package at any time.
                        </p>
                    </details>

                    <details class="border border-gray-300 rounded-lg p-4 cursor-pointer group">
                        <summary class="font-bold text-gray-800 flex justify-between items-center">
                            How long is a package valid?
                            <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                        </summary>
                        <p class="text-gray-600 mt-4">
                            - <strong>Basic:</strong> Lifetime<br>
                            - <strong>Silver:</strong> 1 month<br>
                            - <strong>Gold:</strong> 1 month<br>
                            You can renew or upgrade anytime before the package expires.
                        </p>
                    </details>

                    <details class="border border-gray-300 rounded-lg p-4 cursor-pointer group">
                        <summary class="font-bold text-gray-800 flex justify-between items-center">
                            What if my payment fails?
                            <i class="fas fa-chevron-down group-open:rotate-180 transition"></i>
                        </summary>
                        <p class="text-gray-600 mt-4">
                            If your payment fails, please contact our support team via WhatsApp or email. 
                            We're ready to help you resolve any payment issues.
                        </p>
                    </details>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection