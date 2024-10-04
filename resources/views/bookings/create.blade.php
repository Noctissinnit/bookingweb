@extends('layouts.app')

@section('content')

<style>

body {
        background-color: #f8f9fa;
    }
    .container {
        max-width: 1200px; /* Lebar maksimal kontainer */
    }
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
    .booked {
        background-color: #f8d7da;
    }
    .dashboard {
        margin: 0 auto; /* Agar dashboard berada di tengah */
        max-width: 400px; /* Maksimum lebar dashboard */
    }
    .dashboard-item {
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        background-color: #ffffff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    .delete-btn {
        position: absolute;
        right: 10px;
        top: 10px;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        padding: 5px 10px;
        font-size: 0.875rem;
    }
    .delete-btn:hover {
        background-color: #c82333;
    }
    .room-status {
        font-weight: bold;
    }
    .modal-header {
        background-color: #007bff;
        color: white;
    }
    .modal-footer {
        border-top: none;
    }
    h2, h4 {
        color: #007bff;
    }
    #real-time {
        font-size: 1.2rem;
        color: #343a40;
        text-align: center; /* Tengah */
    }
    /* Style untuk membungkus kalender */
    #calendar {
        max-width: 80%;
        margin: 0 auto;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Style tambahan untuk form */
    .form-container {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Style header FullCalendar (judul dan navigasi) */
    .fc-toolbar {
        background-color: #007bff;
        color: #fff;
        padding: 10px;
        border-radius: 8px 8px 0 0;
    }

    .fc-toolbar h2 {
        font-size: 1.5rem;
        color: #ffffff;
    }

    .fc-button-group .fc-button {
        background-color: #ffffff;
        color: #007bff;
        border: none;
        padding: 10px;
        border-radius: 5px;
        font-weight: bold;
    }

    .fc-button-group .fc-button:hover {
        background-color: #007bff;
        color: #ffffff;
    }

    .fc-button-primary {
        background-color: #ffffff;
        color: #007bff;
        border: none;
    }

    .fc-button-primary:hover {
        background-color: #007bff;
        color: #ffffff;
    }

    /* Style untuk grid hari */
    .fc-daygrid-day {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }

    /* Style untuk hari saat ini */
    .fc-day-today {
        background-color: #007bff !important;
        color: white;
    }

    /* Hover effect pada tanggal */
    .fc-daygrid-day:hover {
        background-color: #007bff;
        color: #ffffff;
        transition: background-color 0.3s ease;
    }

    /* Style untuk event */
    .fc-event {
        background-color: #28a745;
        color: white;
        border-radius: 5px;
        border: none;
        padding: 5px;
        font-size: 0.9rem;
    }

    /* Style pada header grid */
    .fc-col-header-cell {
        background-color: #007bff;
        color: white;
        padding: 10px;
    }

    .fc-daygrid-day-number {
        padding: 8px;
        font-weight: bold;
        color: #007bff;
    }

    /* Style pada Kiri: Input */
    .room-status {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            background-color: #fff;
        }
</style>

<div class="container">
    <div class="row">
        <!-- Bagian Kiri: Input Biodata -->
        <div class="col-md-5">
            <div class="room-status" id="room-status">
                <!-- <h1>Peminjaman Ruang Meeting</h1>
                <div id="current-date"></div>
                <div id="current-time"></div>
                <div id="room-name">Room A</div>
                <div id="room-name">Room B</div>
                <div id="room-name">Room C</div>
                <div id="bookings"></div> -->

                <!-- Real-Time Clock -->
                <h5 id="current-date" class="mb-4"></h5>
                <h5 id="current-time" class="mb-4"></h5>

                <!-- Dashboard -->
                <div class="dashboard">
                    <h2>Dashboard Peminjaman Ruangan</h2>
                    <h4>Ruangan yang Tersedia</h4>
                    <div id="availableRoomsList" class="mb-3">
                        @foreach ($rooms as $room)
                            <div id="room-name">{{ $room->name }}: {{ $room->isBooked() ? ("Tidak Tersedia (".$room->booking()->nama.")") : "Tersedia" }}</div>
                        @endforeach
                    </div>
                    @auth
                        <h4>Peminjaman Saat Ini</h4>
                        <div id="dashboardItems">
                            @foreach ($rooms as $room)
                                @if($room->isBookedAuth())
                                    <div id="room-name">{{ $room->name }}</div>
                                @endif
                            @endforeach
                        </div>
                    @endauth
                </div>

                <!-- Input untuk menambah peminjaman -->
                {{-- <h3>Tambah Peminjaman</h3>
                <input type="text" id="booking-name" placeholder="Nama Tim" />
                <input type="time" id="booking-start" placeholder="Jam Mulai" />
                <input type="time" id="booking-end" placeholder="Jam Selesai" />
                <button onclick="addBooking()">Tambah Peminjaman</button> --}}
            </div>

        </div>

        <!-- Bagian Kanan: Kalender -->
        <div class="col-md-7">
            <div id="calendar" class="shadow-sm rounded" style="background-color: #f8f9fa;"></div>
        </div>
    </div>
</div>

<!-- Modal Login -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="loginForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk form booking -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <form class="modal-dialog modal-lg" id="bookingForm" role="document">
        @csrf
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookingModalLabel">Book Meeting Room</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="selectedDate" name="date">

                <div class="row">
                    <div class="col">
                        <label for="Nama" class="font-weight-bold">Nama</label>
                        <input type="text" class="form-control" name="nama" placeholder="Nama">
                    </div>
                    <div class="col">
                        <label for="Email" class="font-weight-bold">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="NIP" class="font-weight-bold">NIP</label>
                        <input type="text" class="form-control" name="nip" placeholder="NIP" required>
                    </div>
                    <div class="col">
                        <label for="Department" class="font-weight-bold">Department</label>
                        <input type="text" class="form-control" name="department" placeholder="Department" required>
                    </div>
                </div>


                <div class="form-group">
                    <label for="room" class="font-weight-bold">Select Room</label>
                    <select name="room_id" class="form-control" required>
                        @foreach($rooms as $room)
                            @if(!$room->isBooked())
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>



                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="start_time" class="font-weight-bold">Start Time</label>
                        <input type="time" class="form-control" name="start_time" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="end_time" class="font-weight-bold">End Time</label>
                        <input type="time" class="form-control" name="end_time" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="font-weight-bold">Description</label>
                    <textarea class="form-control" name="description" rows="3" required></textarea>
                </div>
                <!-- <div class="form-group">
                    <label>Invitation</label>
                    <select class="select2" name="states[]" multiple="" data-placeholder="Select a State" style="width: 100%;">
                        <option value="Alabama">Alabama</option>
                        <option value="Alaska">Alaska</option>
                        <option value="California">California</option>
                    </select>
                </div> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit Booking</button>
            </div>
        </div>
    </form>
</div>

@endsection

<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.4.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.4.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@4.4.2/main.min.js"></script>

<script>
let isLoggedIn = {{ auth()->check() ? "true" : "false" }};
let bookings = {};

const loginUrl = "{{ route('bookings.login') }}";
const storeUrl = "{{ route('bookings.store') }}";

// Daftar ruangan
const availableRooms = [];

document.addEventListener('DOMContentLoaded', function() {
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
            // Tampilkan modal saat user klik tanggal di kalender
            // $('#bookingModal').modal('show');
            if (isLoggedIn) {
                // selectedDateBooking.value = dateString; // Set tanggal yang dipilih
                $('#selectedDate').val(info.dateStr);
                const bookingDetails = bookings[info];
                if (bookingDetails) {
                    alert(`Ruangan sudah dipinjam:\nRuang: ${bookingDetails.room}\nMulai: ${bookingDetails.startTime}\nAkhir: ${bookingDetails.endTime}\nPeserta: ${bookingDetails.participants}\nDeskripsi: ${bookingDetails.description}`);
                } else {
                    const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
                    bookingModal.show();
                }
            } else {
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
            }
        }
    });
    calendar.render();

    $('.select2').select2({
        dropdownParent: $('#bookingModal')
    });

    $('#loginForm').submit(async (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        // const user = registeredUsers.find(user => user.username === username && user.password === password);
        const response = await $.get(loginUrl, { email, password });
        if (response.success) {
            location.reload();
            // isLoggedIn = true; // Set login status
            // alert('Login Berhasil!');
            // const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
            // loginModal.hide();
            // const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
            // bookingModal.show(); // Tampilkan modal peminjaman setelah login
        } else {
            alert('Username atau Password salah!');
        }
    });

    $('#bookingForm').submit(async (e) => {
        e.preventDefault();
        // const room = document.getElementById('roomSelect').value;
        // const startTime = document.getElementById('startTime').value;
        // const endTime = document.getElementById('endTime').value;
        // const participants = document.getElementById('participants').value;
        // const description = document.getElementById('description').value;
        // const bookingDate = selectedDateBooking.value;

        // Simpan peminjaman
        // bookings[bookingDate] = { room, startTime, endTime, description };
        await $.post(storeUrl, $('#bookingForm').serialize());
        updateDashboard();

        // Setelah menyimpan, logout otomatis
        isLoggedIn = false; // Set login status ke false
        const bookingModal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
        bookingModal.hide();
        alert('Peminjaman Ruangan berhasil disimpan!');
        location.reload();

        // Reset form
        // $('#bookingForm').reset();
    });


    // Check for expired bookings
    setInterval(() => {
        const now = new Date();
        for (const [date, details] of Object.entries(bookings)) {
            const endDateTime = new Date(`${date}T${details.endTime}`);
            if (now >= endDateTime) {
                delete bookings[date]; // Hapus peminjaman yang sudah berakhir
                updateDashboard(); // Update dashboard
            }
        }
    }, 60000); // Cek setiap 60 detik

    updateDashboard();
});

function updateDateTime() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = now.toLocaleDateString('id-ID', options);
    const formattedTime = now.toLocaleTimeString('id-ID');

    // document.getElementById('current-date').innerText = formattedDate;
    document.getElementById('current-time').innerText = formattedTime;
}

// Update Dashboard
function updateDashboard() {
    $('#dashboardItems').innerHTML = '';
    const availableRoomsStatus = {};

    // Set semua ruangan sebagai tersedia
    for (const room of availableRooms) {
        availableRoomsStatus[room] = "Tersedia"; // Set status awal
    }

    for (const [date, details] of Object.entries(bookings)) {
        const item = document.createElement('div');
        item.classList.add('dashboard-item');
        item.innerHTML = `
            <strong>Tanggal: ${date}</strong><br>
            Ruang: ${details.room}<br>
            Mulai: ${details.startTime}<br>
            Akhir: ${details.endTime}<br>
            Peserta: ${details.participants}<br>
            Deskripsi: ${details.description}
            <button class="delete-btn" onclick="deleteBooking('${date}')">Hapus</button>
        `;
        dashboardItems.appendChild(item);

        // Ubah status ruangan menjadi tidak tersedia
        availableRoomsStatus[details.room] = "Tidak Tersedia";
    }

    // Menampilkan status ruangan
    $('#availableRoomsList').innerHTML = "Ruangan yang Tersedia:<br>";
    for (const [room, status] of Object.entries(availableRoomsStatus)) {
        $('#availableRoomsList').innerHTML += `${room}: <span class="room-status">${status}</span><br>`;
    }
}

function updateRoomStatus() {
    const now = new Date();
    const currentHour = now.getHours();
    const currentMinute = now.getMinutes();
    const currentTotalMinutes = currentHour * 60 + currentMinute;

    let isAvailable = true;

    bookingsData.forEach(booking => {
        const [startHour, startMinute] = booking.start.split(':').map(Number);
        const [endHour, endMinute] = booking.end.split(':').map(Number);

        const startTotalMinutes = startHour * 60 + startMinute;
        const endTotalMinutes = endHour * 60 + endMinute;

        if (currentTotalMinutes >= startTotalMinutes && currentTotalMinutes < endTotalMinutes) {
            isAvailable = false;
        }
    });

    // const status = isAvailable ? "Tersedia" : "Tidak Tersedia";
    document.getElementById('room-name').innerText = `Ruang 101 - Status: ${status}`;

    const bookingsDiv = document.getElementById('bookings');
    bookingsDiv.innerHTML = '<h3>Jam Penggunaan Hari Ini:</h3>';

    if (bookingsData.length === 0) {
        bookingsDiv.innerHTML += '<p>Ruang tersedia hari ini.</p>';
    } else {
        bookingsData.forEach((booking, index) => {
            const bookingInfo = document.createElement('div');
            const [startHour, startMinute] = booking.start.split(':').map(Number);
            const [endHour, endMinute] = booking.end.split(':').map(Number);
            const startTotalMinutes = startHour * 60 + startMinute;
            const endTotalMinutes = endHour * 60 + endMinute;

            if (currentTotalMinutes >= startTotalMinutes && currentTotalMinutes < endTotalMinutes) {
                bookingInfo.style.backgroundColor = '#87CEFA';
            }

            bookingInfo.innerText = `Dipinjam oleh ${booking.name} dari ${booking.start} hingga ${booking.end}`;

            const deleteButton = document.createElement('button');
            deleteButton.innerText = 'Hapus';
            deleteButton.onclick = () => deleteBooking(index);
            bookingInfo.appendChild(deleteButton);

            bookingsDiv.appendChild(bookingInfo);
        });
    }
}

/*function deleteBooking(index) {
    bookingsData.splice(index, 1); // Hapus peminjaman dari array
    updateRoomStatus(); // Memperbarui status setelah menghapus peminjaman
}*/
function deleteBooking(date) {
    delete bookings[date];
    updateDashboard();
    alert(`Peminjaman pada tanggal ${date} telah dihapus.`);
}

/*function addBooking() {
    const name = document.getElementById('booking-name').value;
    const start = document.getElementById('booking-start').value;
    const end = document.getElementById('booking-end').value;

    if (name && start && end) {
        bookingsData.push({ start, end, name });
        updateRoomStatus();
        document.getElementById('booking-name').value = '';
        document.getElementById('booking-start').value = '';
        document.getElementById('booking-end').value = '';
    } else {
        alert('Silakan isi semua kolom!');
    }
}*/

function generateCalendar() {
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth();

    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();

    const calendarDiv = document.getElementById('calendar');
    calendarDiv.innerHTML = '';

    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    const table = document.createElement('table');
    const headerRow = document.createElement('tr');

    dayNames.forEach(day => {
        const th = document.createElement('th');
        th.innerText = day;
        headerRow.appendChild(th);
    });

    table.appendChild(headerRow);

    let dateRow = document.createElement('tr');

    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('td');
        dateRow.appendChild(emptyCell);
    }

    for (let date = 1; date <= lastDate; date++) {
        const dateCell = document.createElement('td');
        dateCell.innerText = date;

        if (date === now.getDate()) {
            dateCell.classList.add('today');
        }

        dateRow.appendChild(dateCell);

        if ((date + firstDay) % 7 === 0) {
            table.appendChild(dateRow);
            dateRow = document.createElement('tr');
        }
    }

    if (dateRow.children.length > 0) {
        table.appendChild(dateRow);
    }

    calendarDiv.appendChild(table);
}

generateCalendar();

/*setInterval(() => {
    updateDateTime();
    // updateRoomStatus();
}, 1000);*/
</script>
