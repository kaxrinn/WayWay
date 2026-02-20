@extends('layouts.app')

@section('title', 'Profil Wisatawan')

@section('content')
<div class="min-h-screen bg-sky-50 flex justify-center items-center pt-24 pb-10">
    <div class="bg-white border border-sky-100 shadow-md rounded-2xl
                w-full max-w-md mx-4 p-6">

        <!-- HEADER -->
        <div class="flex flex-col items-center border-b border-sky-100 pb-4 mb-5">

            <!-- ICON -->
            <div class="bg-[#5b9ac7] text-white rounded-full p-4 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-8 w-8"
                     fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A7 7 0 0112 15a7 7 0 016.879 2.804
                             M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>

            <h2 class="text-base font-semibold text-slate-800">
                {{ $user->name }}
            </h2>
            <p class="text-sm text-slate-500">
                {{ $user->email }}
            </p>
        </div>

        <!-- INFO -->
        <div class="space-y-4 text-sm">

            <div>
                <p class="text-slate-500">Nama Lengkap</p>
                <p class="font-medium text-slate-800">
                    {{ $user->name }}
                </p>
            </div>

            <div>
                <p class="text-slate-500">Email</p>
                <p class="font-medium text-slate-800">
                    {{ $user->email }}
                </p>
            </div>

        </div>

        <!-- ACTION -->
        <div class="flex justify-center mt-7">
            <a href="{{ route('wisatawan.beranda') }}"
               class="bg-[#5b9ac7] hover:bg-[#496d9e]
                      text-white font-medium
                      rounded-full px-6 py-2 text-sm transition">
                Kembali ke Beranda
            </a>
        </div>

    </div>
</div>
@endsection
