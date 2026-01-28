@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

    {{-- Hero Section --}}
    @include('wisatawan.berandasection.banner')
    @include('wisatawan.berandasection.destinasi')
    @include('wisatawan.berandasection.iklan')
    @include('wisatawan.berandasection.map')
    @include('wisatawan.berandasection.tentang')
    @include('wisatawan.berandasection.kontak')

@endsection
