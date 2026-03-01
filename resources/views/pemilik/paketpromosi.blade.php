@extends('layouts.pemilik')

@section('title', 'Promotion Packages')

@section('content')
<!-- Header -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-rocket text-purple-500"></i>
            WayWay Promotion Packages
        </h1>
        <p class="text-gray-500 mt-2 text-sm sm:text-base">Choose the package that suits your business needs</p>
    </div>
</div>

<!-- Current Package Info -->
<div class="bg-gradient-to-r from-primary to-blue-400 rounded-xl p-4 sm:p-6 mb-8 text-white shadow-lg">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-white/80 mb-1 text-sm sm:text-base">Currently Active Package</p>
            <h2 class="text-2xl sm:text-3xl font-bold">{{ $currentPaket ? $currentPaket->nama_paket : 'No Active Package' }}</h2>
            @if($currentPaket && auth()->user()->paket_expired_at)
                <p class="text-sm text-white/90 mt-2">
                    Expires: {{ auth()->user()->paket_expired_at->format('d M Y') }}
                    @if($isPaketExpired)
                        <span class="bg-red-500 px-2 py-1 rounded ml-2 text-xs">EXPIRED</span>
                    @endif
                </p>
            @elseif($currentPaket)
                <p class="text-sm text-white/90 mt-2">Active Forever ♾️</p>
            @endif
        </div>
        <div class="bg-white/20 p-3 sm:p-4 rounded-full flex-shrink-0">
            <i class="fas fa-box text-3xl sm:text-5xl"></i>
        </div>
    </div>
</div>

<!-- Package Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    @foreach($pakets as $paket)
    @php
        $isCurrentPaket = $currentPaket->id == $paket->id;
        $colors = [
            'Basic' => ['border' => 'blue', 'bg' => 'blue', 'icon' => 'fa-box'],
            'Standard' => ['border' => 'purple', 'bg' => 'purple', 'icon' => 'fa-star'],
            'Premium' => ['border' => 'yellow', 'bg' => 'yellow', 'icon' => 'fa-crown'],
        ];
        $color = $colors[$paket->nama_paket] ?? ['border' => 'gray', 'bg' => 'gray', 'icon' => 'fa-box'];
    @endphp
    
    <div class="bg-white rounded-xl shadow-xl overflow-hidden border-4 border-{{ $color['border'] }}-400 hover:shadow-2xl transition transform hover:-translate-y-2 {{ $isCurrentPaket ? 'ring-4 ring-green-500' : '' }}">
        <!-- Header -->
        <div class="bg-gradient-to-br from-{{ $color['bg'] }}-500 to-{{ $color['bg'] }}-600 p-5 sm:p-6 text-white relative">
            @if($isCurrentPaket && !$isPaketExpired)
            <div class="absolute top-3 right-3">
                <span class="bg-green-500 text-white px-2 sm:px-3 py-1 rounded-full text-xs font-bold">
                    <i class="fas fa-check-circle"></i>
                    Active Package
                </span>
            </div>
            @endif
            
            <div class="flex items-center gap-3 mb-4">
                <i class="fas {{ $color['icon'] }} text-3xl sm:text-4xl"></i>
                <h3 class="text-xl sm:text-2xl font-bold">{{ $paket->nama_paket }}</h3>
            </div>
            
            <div class="mb-2">
                @if($paket->harga == 0)
                    <p class="text-3xl sm:text-4xl font-bold">FREE</p>
                    <p class="text-sm text-white/80">Forever</p>
                @else
                    <p class="text-3xl sm:text-4xl font-bold">Rp {{ number_format($paket->harga, 0, ',', '.') }}</p>
                    <p class="text-sm text-white/80">per month</p>
                @endif
            </div>
        </div>
        
        <!-- Features -->
        <div class="p-5 sm:p-6">
            <p class="text-gray-600 text-sm mb-4">{{ $paket->deskripsi }}</p>
            
            <div class="space-y-3 mb-6">
                <div class="flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span class="text-sm">
                        <strong>{{ $paket->max_destinasi ?? '∞' }}</strong> active destinations
                    </span>
                </div>
                
                <div class="flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span class="text-sm">
                        Max <strong>{{ $paket->max_foto ?? '∞' }}</strong> photos per destination
                    </span>
                </div>
                
                @if($paket->max_video > 0)
                <div class="flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span class="text-sm">
                        Max <strong>{{ $paket->max_video }}</strong> videos per destination
                    </span>
                </div>
                @endif
                
                <div class="flex items-start gap-2">
                    @if($paket->can_edit_foto)
                        <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                        <span class="text-sm">Edit photos <strong>directly</strong></span>
                    @else
                        <i class="fas fa-times text-red-500 mt-1 flex-shrink-0"></i>
                        <span class="text-sm text-gray-500">Edit via <strong>request</strong></span>
                    @endif
                </div>
                
                @if($paket->is_featured_allowed)
                <div class="flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span class="text-sm">
                        <strong class="text-yellow-600">Featured</strong> on homepage
                    </span>
                </div>
                @endif
                
                <!-- Extra Features from fitur field -->
                @foreach(explode(',', $paket->fitur) as $fitur)
                <div class="flex items-start gap-2">
                    <i class="fas fa-check text-green-500 mt-1 flex-shrink-0"></i>
                    <span class="text-sm">{{ trim($fitur) }}</span>
                </div>
                @endforeach
            </div>
            
            <!-- Action Button -->
            @if($paket->harga == 0)
                @if($isCurrentPaket)
                    <button disabled class="w-full bg-gray-300 text-gray-600 px-6 py-3 rounded-lg font-semibold cursor-not-allowed text-sm sm:text-base">
                        <i class="fas fa-check mr-2"></i>
                        Free Package Active
                    </button>
                @else
                    <button disabled class="w-full bg-gray-300 text-gray-600 px-6 py-3 rounded-lg font-semibold cursor-not-allowed text-sm sm:text-base">
                        Free Package
                    </button>
                @endif
            @else
                @if($isCurrentPaket && !$isPaketExpired)
                    <form method="POST" action="{{ route('pemilik.paket.checkout', $paket->id) }}">
                        @csrf
                        <input type="hidden" name="durasi" value="monthly">
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-lg hover:shadow-xl text-sm sm:text-base">
                            <i class="fas fa-sync mr-2"></i>
                            Renew Package
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('pemilik.paket.checkout', $paket->id) }}" x-data="{ durasi: 'monthly' }">
                        @csrf
                        
                        <!-- Duration Selection -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Select Duration:</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="durasi" value="monthly" x-model="durasi" class="hidden peer">
                                    <div class="border-2 border-gray-300 peer-checked:border-{{ $color['bg'] }}-500 peer-checked:bg-{{ $color['bg'] }}-50 rounded-lg p-2 sm:p-3 text-center transition">
                                        <p class="font-bold text-sm">Monthly</p>
                                        <p class="text-xs text-gray-600">Rp {{ number_format($paket->harga, 0, ',', '.') }}</p>
                                    </div>
                                </label>
                                
                                <label class="cursor-pointer">
                                    <input type="radio" name="durasi" value="yearly" x-model="durasi" class="hidden peer">
                                    <div class="border-2 border-gray-300 peer-checked:border-{{ $color['bg'] }}-500 peer-checked:bg-{{ $color['bg'] }}-50 rounded-lg p-2 sm:p-3 text-center transition relative">
                                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full font-bold">-10%</span>
                                        <p class="font-bold text-sm">Yearly</p>
                                        <p class="text-xs text-gray-600">Rp {{ number_format($paket->harga * 12 * 0.9, 0, ',', '.') }}</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-{{ $color['bg'] }}-500 to-{{ $color['bg'] }}-600 hover:from-{{ $color['bg'] }}-600 hover:to-{{ $color['bg'] }}-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-lg hover:shadow-xl text-sm sm:text-base">
                            <i class="fas fa-rocket mr-2"></i>
                            Upgrade Now
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
    @endforeach
</div>

<!-- Comparison Table -->
<div class="bg-white rounded-xl shadow-xl overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-primary to-blue-400 px-4 sm:px-6 py-4">
        <h2 class="text-xl sm:text-2xl font-bold text-white">📊 Package Comparison</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full min-w-[480px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 sm:px-6 py-4 text-left text-sm font-semibold text-gray-700">Feature</th>
                    @foreach($pakets as $paket)
                        <th class="px-4 sm:px-6 py-4 text-center text-sm font-semibold text-gray-700">{{ $paket->nama_paket }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr>
                    <td class="px-4 sm:px-6 py-4 font-medium text-sm">Max Destinations</td>
                    @foreach($pakets as $paket)
                        <td class="px-4 sm:px-6 py-4 text-center text-sm">{{ $paket->max_destinasi ?? '∞' }}</td>
                    @endforeach
                </tr>
                <tr class="bg-gray-50">
                    <td class="px-4 sm:px-6 py-4 font-medium text-sm">Max Photos/Destination</td>
                    @foreach($pakets as $paket)
                        <td class="px-4 sm:px-6 py-4 text-center text-sm">{{ $paket->max_foto ?? '∞' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="px-4 sm:px-6 py-4 font-medium text-sm">Max Videos/Destination</td>
                    @foreach($pakets as $paket)
                        <td class="px-4 sm:px-6 py-4 text-center text-sm">{{ $paket->max_video ?? '0' }}</td>
                    @endforeach
                </tr>
                <tr class="bg-gray-50">
                    <td class="px-4 sm:px-6 py-4 font-medium text-sm">Direct Editing</td>
                    @foreach($pakets as $paket)
                        <td class="px-4 sm:px-6 py-4 text-center">
                            @if($paket->can_edit_foto)
                                <i class="fas fa-check text-green-500 text-lg sm:text-xl"></i>
                            @else
                                <i class="fas fa-times text-red-500 text-lg sm:text-xl"></i>
                            @endif
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <td class="px-4 sm:px-6 py-4 font-medium text-sm">Featured on Homepage</td>
                    @foreach($pakets as $paket)
                        <td class="px-4 sm:px-6 py-4 text-center">
                            @if($paket->is_featured_allowed)
                                <i class="fas fa-check text-green-500 text-lg sm:text-xl"></i>
                            @else
                                <i class="fas fa-times text-red-500 text-lg sm:text-xl"></i>
                            @endif
                        </td>
                    @endforeach
                </tr>
                <tr class="bg-gray-50">
                    <td class="px-4 sm:px-6 py-4 font-medium text-sm">Price/Month</td>
                    @foreach($pakets as $paket)
                        <td class="px-4 sm:px-6 py-4 text-center font-bold text-sm">
                            @if($paket->harga == 0)
                                FREE
                            @else
                                Rp {{ number_format($paket->harga, 0, ',', '.') }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- FAQ -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mt-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-question-circle text-primary"></i>
        Frequently Asked Questions
    </h2>
    
    <div class="space-y-4" x-data="{ open: null }">
        <div class="border border-gray-200 rounded-lg">
            <button @click="open = open === 1 ? null : 1" 
                    class="w-full px-4 sm:px-6 py-4 text-left font-semibold text-gray-800 hover:bg-gray-50 transition flex items-center justify-between gap-3 text-sm sm:text-base">
                <span>How do I upgrade my package?</span>
                <i class="fas fa-chevron-down transition flex-shrink-0" :class="open === 1 ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open === 1" x-cloak class="px-4 sm:px-6 py-4 bg-gray-50 text-gray-600 text-sm sm:text-base">
                <p>Select the desired package, choose a duration (monthly/yearly), then click "Upgrade Now". You will be redirected to the payment page.</p>
            </div>
        </div>
        
        <div class="border border-gray-200 rounded-lg">
            <button @click="open = open === 2 ? null : 2" 
                    class="w-full px-4 sm:px-6 py-4 text-left font-semibold text-gray-800 hover:bg-gray-50 transition flex items-center justify-between gap-3 text-sm sm:text-base">
                <span>What happens when my package expires?</span>
                <i class="fas fa-chevron-down transition flex-shrink-0" :class="open === 2 ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open === 2" x-cloak class="px-4 sm:px-6 py-4 bg-gray-50 text-gray-600 text-sm sm:text-base">
                <p>If your package expires, your account will automatically be downgraded to the Basic plan. Destinations that exceed the Basic limit will become inactive until you upgrade again.</p>
            </div>
        </div>
        
        <div class="border border-gray-200 rounded-lg">
            <button @click="open = open === 3 ? null : 3" 
                    class="w-full px-4 sm:px-6 py-4 text-left font-semibold text-gray-800 hover:bg-gray-50 transition flex items-center justify-between gap-3 text-sm sm:text-base">
                <span>Is there a discount for yearly billing?</span>
                <i class="fas fa-chevron-down transition flex-shrink-0" :class="open === 3 ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open === 3" x-cloak class="px-4 sm:px-6 py-4 bg-gray-50 text-gray-600 text-sm sm:text-base">
                <p>Yes! Yearly billing gets a 10% discount off the total price. Example: Standard Rp 49K/month x 12 = Rp 588K, with discount it becomes Rp 529K/year.</p>
            </div>
        </div>
    </div>
</div>
@endsection