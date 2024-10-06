@extends('layouts.app')

@section('head')
<link rel="stylesheet" href="/css/bookings/create.css">

<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.4.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.4.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@4.4.2/main.min.js"></script>

<script>
const isAuth = @if(Auth::check()) true @else false @endif;
const listUrl = "{{ route('bookings.list') }}";
const loginUrl = "{{ route('bookings.login') }}";
const storeUrl = "{{ route('bookings.store') }}";
const destroyUrl = "{{ route('bookings.destroy') }}";
</script>
<script src="/js/bookings/create.js"></script>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-7 room-card" id="room-status">
            <div id="current-date"></div>
            <div id="current-time"></div>
        </div>
        <div class="col-md-5 room-card">
            <h4>List Ruangan</h4>
            <div id="availableRoomsList" class="mb-3">
                @foreach ($rooms as $room)
                    <div class="room-name">{{ $room->name }}: {{ $room->isBooked() ? ("Tidak Tersedia (".$room->booking()->nama.")") : "Tersedia" }}</div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-7" id="calendar"></div>
        <div class="col-md-5">
            <div id="current-bookings" class="border p-2">
                <h4>Jam Penggunaan Hari Ini:</h4>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Login -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" id="login-nis" class="form-control" placeholder="NIS" />
                </div>
                <div class="form-group">
                    <input type="password" id="login-password" class="form-control" placeholder="Password" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button onclick="checkLogin()" class="btn btn-primary">Login</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk menambah peminjaman -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content" action="{{ route('bookings.store') }}" method="POST">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Tambah Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @csrf
                <input type="hidden" name="date">
                <input type="hidden" name="nis">
                <input type="hidden" name="password">
                <div class="form-group">
                    <input type="text" class="form-control" name="nama" placeholder="Nama" />
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Email" />
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="nip" placeholder="NIP" required>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="department" placeholder="Department" required>
                </div>
                <div class="row form-group">
                    <div class="col">
                        <input type="time" class="form-control" name="start_time" placeholder="Jam Mulai" />
                    </div>
                    <div class="col">
                        <input type="time" class="form-control" name="end_time" placeholder="Jam Selesai" />
                    </div>
                </div>
                <div class="form-group">
                    <select name="room_id" id="select-room" class="form-control" style="width: 100%" required>
                        @foreach($rooms as $room)
                            @if(!$room->isBooked())
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="description" class="font-weight-bold">Description</label>
                    <textarea class="form-control" name="description" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button class="btn btn-primary">Tambah Peminjaman</button>
            </div>
        </form>
    </div>
</div>
@endsection
