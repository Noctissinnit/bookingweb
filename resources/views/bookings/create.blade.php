@extends('layouts.app')

@section('content')

<style>
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
                <h1>Peminjaman Ruang Meeting</h1>
                <div id="current-date"></div>
                <div id="current-time"></div>
                <div id="room-name">Room A</div>
                <div id="room-name">Room B</div>
                <div id="room-name">Room C</div>
                <div id="bookings"></div>

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

<!-- Modal untuk form booking -->
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookingModalLabel">Book Meeting Room</h5>
            </div>

                <div class="modal-body">
                    <input type="hidden" id="selectedDate" name="date">

                    <form>
                        <div class="row">
                          <div class="col">
                            <label for="User" class="font-weight-bold">User</label>
                            <input type="text" class="form-control" placeholder="Username">
                          </div>
                          <div class="col">
                            <label for="Password" class="font-weight-bold">Password</label>
                            <input type="text" class="form-control" placeholder="Password">
                          </div>
                        </div>
                      </form>



                    <div class="form-group">
                        <label for="room" class="font-weight-bold">Select Room</label>
                        <select name="room_id" class="form-control" required>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
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
                    <div class="form-group">
                        <label>Invitation</label>
                        <select class="select2" name="states[]" multiple="" data-placeholder="Select a State" style="width: 100%;">
                            <option value="Alabama">Alabama</option>
                            <option value="Alaska">Alaska</option>
                            <option value="California">California</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.4.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.4.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@4.4.2/main.min.js"></script>

<script>
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
            $('#selectedDate').val(info.dateStr);
            $('#bookingModal').modal('show');
        }
    });
    calendar.render();

    $('.select2').select2({
        dropdownParent: $('#bookingModal')
    });
});

function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = now.toLocaleDateString('id-ID', options);
            const formattedTime = now.toLocaleTimeString('id-ID');

            document.getElementById('current-date').innerText = formattedDate;
            document.getElementById('current-time').innerText = formattedTime;
        }

        const bookingsData = [
            { start: '16:49', end: '16:50', name: 'Tim C' },
        ];

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

            const status = isAvailable ? "Tersedia" : "Tidak Tersedia";
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

        function deleteBooking(index) {
            bookingsData.splice(index, 1); // Hapus peminjaman dari array
            updateRoomStatus(); // Memperbarui status setelah menghapus peminjaman
        }

        function addBooking() {
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
        }

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

        setInterval(() => {
            updateDateTime();
            updateRoomStatus();
        }, 1000);

        generateCalendar();
</script>
