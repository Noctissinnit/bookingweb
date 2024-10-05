@extends('layouts.app')

@section('content')

<style>
    body {
        background-color: #f9f9f9;
        padding: 20px;
    }
    .room-status {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 5px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    #current-date, #current-time {
        font-size: 18px;
        margin: 10px 0;
    }
    #room-name {
        font-size: 24px;
        font-weight: bold;
        margin: 20px 0;
    }
    #current-bookings {
        margin: 20px 0;
        text-align: left;
    }
    .today {
        background-color: #87CEFA;
    }
    .form-group {
        margin-top: .5rem;
    }
    /* Styling khusus kalender */
    .calendar {
        max-width: 400px; /* Maksimum lebar kalender */
        margin: 0 auto; /* Agar kalender berada di tengah */
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        background-color: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .day {
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ddd;
        cursor: pointer;
        position: relative;
        transition: background-color 0.3s;
    }
    .day-header {
        background-color: #f1f1f1;
        font-weight: bold;
    }
    .day:hover {
        background-color: #e9ecef;
    }
    /* Styling khusus kalender */
</style>
<div class="container">
    <div class="room-status" id="room-status">
        <h1 class="text-center">Status Ruang</h1>
        <div id="current-date" class="text-center"></div>
        <div id="current-time" class="text-center"></div>
        <div id="room-name" class="text-center">Ruang 101</div>

        <!-- Tombol untuk menambah peminjaman -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Tambah Peminjaman</button>
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
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Tambah Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="text" id="booking-name" class="form-control" placeholder="Nama Tim" />
                </div>
                <div class="form-group">
                    <input type="time" id="booking-start" class="form-control" placeholder="Jam Mulai" />
                </div>
                <div class="form-group">
                    <input type="time" id="booking-end" class="form-control" placeholder="Jam Selesai" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button onclick="addBooking()" class="btn btn-primary">Tambah Peminjaman</button>
            </div>
        </div>
    </div>
</div>

@endsection

<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.4.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.4.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@4.4.2/main.min.js"></script>

<script>
const loginUrl = "{{ route('bookings.login') }}";
const storeUrl = "{{ route('bookings.store') }}";

document.addEventListener("DOMContentLoaded", () => {
    generateCalendar();
});

function generateCalendar(){
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [ 'dayGrid', 'interaction' ],
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
        },
        initialView: 'dayGridMonth',
        dateClick: function(info) {
            
        }
    });
    calendar.render();
}

function updateDateTime() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = now.toLocaleDateString('id-ID', options);
    const formattedTime = now.toLocaleTimeString('id-ID');

    document.getElementById('current-date').innerText = formattedDate;
    document.getElementById('current-time').innerText = formattedTime;
}

async function checkLogin() {
    const nis = document.getElementById('login-nis').value;
    const password = document.getElementById('login-password').value;

    const res = await $.post(loginUrl, { nis, password });
    if(res.success){
        $('#loginModal').modal('hide');
        $('#bookingModal').modal('show');
    } else {
        alert('NIS atau Password salah. Silakan coba lagi.');
    }
}
setInterval(() => {
    updateDateTime();
}, 1000);
</script>
