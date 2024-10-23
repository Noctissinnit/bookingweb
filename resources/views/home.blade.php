@extends('layouts.app')

@section('head')
<link rel="stylesheet" href="/css/home.css">
@endsection

@section('content')
<div class="container">
    <h1>Peminjaman Ruang</h1>
    <div class="card-container">
        @foreach($rooms as $room)
            <a href="{{ route('bookings.create', $room->id) }}" class="card">
                <img src="https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg" alt="Ruang A">
                <div class="card-content">
                    <h2>{{ $room->name }}</h2>
                    <p>Pilih {{ $room->name }} untuk acara rapat atau pertemuan kecil.</p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
