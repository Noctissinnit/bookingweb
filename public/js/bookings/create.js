let isBookingPost = false;

$(document).ready(() => {
    generateCalendar();
    initTimepickers();
    updateDateTime();
    updateBookings();
    clearForms();

    $("#select-room").select2({
        dropdownParent: $("#bookingModal"),
        width: "resolve",
    });
    $("#select-members").select2({
        dropdownParent: $("#bookingModal"),
        width: "resolve",
    });
    // $('#btn-add-booking').click(() => {
    //     const today = new Date();
    //     $('#form-booking>input[name="date"]').val(today.toISOString().substring(0,10));
    //     $("#loginModal").modal("show");
    // });
    $('#btn-history-add-booking').click(function(){
        $('#bookingHistoryModal').modal('hide');
        $("#loginModal").modal("show");
    });
    
    $("#form-login").submit(checkLogin);
    $('#form-booking').submit(async e => {
        e.preventDefault();
        if(isBookingPost) return;
        
        const formData = new FormData(e.currentTarget);
        if(isTimeLess(formData.get("end_time"), formData.get("start_time"))){
            alert("Jam Selesai tidak bisa kurang dari Jam Mulai.");
            e.preventDefault();
            return;
        }

        const rooms = await $.get(roomListUrl);
        let bookings = rooms.filter(dat => dat.id === roomId)[0].bookings;
        if(bookings.length > 0){
            const bookingsToday = bookings.filter(dat => isToday(dat.date));
            if(bookingsToday.some(dat => isTimeRangeOverlap(formData.get("start_time"), formData.get("end_time"), formatTime(dat.start_time), formatTime(dat.end_time)))){
                alert("Jam peminjaman sudah digunakan oleh user lain.");
                return;
            }
        }
        isBookingPost = true;
        await $.post($('#form-booking').attr('action'), $('#form-booking').serialize());
        location.reload();
    });
    
    $('button[data-bs-dismiss="modal"]').click(clearForms);
});

async function showBookingHistory(date, dateStr){
    $('#bookingHistoryDate').html(dateStr);

    const url = new URL(listUrl);
    url.searchParams.set('date', dateStr);
    url.searchParams.set('room_id', roomId);

    const bookingsData = await $.get(url.toString());
    const tableBody = $('#bookingHistoryTable>tbody');

    tableBody.html('');
    if(bookingsData.length > 0){
        bookingsData.forEach((data, i) => {
            tableBody.append(`
                <tr>


                    <td>${data.department}</td>
                    <td>${formatTime(data.start_time)}</td>
                    <td>${formatTime(data.end_time)}</td>
                    <td>${data.description}</td>
                </tr>
            `)
        });
    } else {
        tableBody.html(`<tr><td colspan="7">Tidak ada data peminjaman...</td></tr>`)
    }

    $('#form-booking>input[name="date"]').val(dateStr);
    $('#btn-history-add-booking').css('display', isAtLeastOneDayLess(date, new Date()) ? 'none' : '');

    $('#bookingHistoryModal').modal('show');
}

function initTimepickers(){
    $('input.timepicker').datetimepicker({
        datepicker: false,
        format: 'H:i',
    })
}

function generateCalendar() {
    var calendarEl = document.getElementById("calendar");
    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ["dayGrid", "interaction"],
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth",
        },
        initialView: "dayGridMonth",
        dateClick: function (info) {
            showBookingHistory(info.date, info.dateStr);
        },
    });
    calendar.render();
}

function isToday(dateString) {
    // Create a Date object from the input string
    const inputDate = new Date(dateString);

    // Get today's date
    const today = new Date();

    // Check if the input date is today by comparing the year, month, and day
    return inputDate.getFullYear() === today.getFullYear() &&
           inputDate.getMonth() === today.getMonth() &&
           inputDate.getDate() === today.getDate();
}

function isTimeRangeOverlap(start1, end1, start2, end2) {
    // Convert times to Date objects for comparison
    const formatTime = (time) => new Date(`1970-01-01T${time}:00`);

    const startTime1 = formatTime(start1);
    const endTime1 = formatTime(end1);
    const startTime2 = formatTime(start2);
    const endTime2 = formatTime(end2);

    // Check if the two time ranges overlap
    return startTime1 < endTime2 && startTime2 < endTime1;
}

function isAtLeastOneDayLess(date1, date2) {
    const differenceInTime = date2.getTime() - date1.getTime();
    const oneDayInMilliseconds = 24 * 60 * 60 * 1000;
    return differenceInTime > oneDayInMilliseconds;
}

function isTimeLess(time1, time2) {
  // Parse the times as hours and minutes (assuming "HH:mm" format)
  const [hours1, minutes1] = time1.split(':').map(Number);
  const [hours2, minutes2] = time2.split(':').map(Number);

  // Create Date objects for comparison
  const date1 = new Date();
  date1.setHours(hours1, minutes1);

  const date2 = new Date();
  date2.setHours(hours2, minutes2);

  // Compare the two times
  return date1 < date2;
}

function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    };
    const formattedDate = now.toLocaleDateString("id-ID", options);
    const formattedTime = now.toLocaleTimeString("id-ID");

    document.getElementById("current-date").innerText = formattedDate;
    document.getElementById("current-time").innerText = formattedTime;
}

async function updateBookings() {
    const url = new URL(listUrl);
    url.searchParams.set('date', new Date().toISOString().substring(0,10));
    url.searchParams.set('room_id', roomId);

    const bookingsData = await $.get(url.toString());

    const currentBookingsDiv = $("#current-bookings");
    currentBookingsDiv.html("<h4>Jam Penggunaan Hari Ini:</h4>");

    if (bookingsData.length === 0) {
        currentBookingsDiv.append("<p>Tidak ada peminjaman hari ini.</p>");
    } else {
        bookingsData.forEach((booking, index) => {
            currentBookingsDiv.append(`
            <div>
                <span>Dipinjam oleh ${booking.nama} dari ${formatTime(booking.start_time)} hingga ${formatTime(booking.end_time)}</span>
                ${isAuth ? `<a href="${destroyUrl}?id=${booking.id}"><button class="btn btn-danger btn-sm ml-2">Hapus</button></a>` : ""}
            </div>
            `);
        });
    }
}

function formatTime(time) {
    let parts = time.split(":");
    if (parts.length === 3) {
        return parts.slice(0, 2).join(":");
    }
    return time;
}

async function checkLogin(e) {
    e.preventDefault();

    const nis = document.getElementById("login-nis").value;
    const password = document.getElementById("login-password").value;

    const res = await $.post(loginUrl, { nis, password });
    if (res.success) {
        $('#form-booking>input[name="room_id"]').val(roomId);
        $('#form-booking>input[name="nis"]').val(nis);
        $('#form-booking>input[name="password"]').val(password);
        $('#form-booking>input[name="nama"]').val(res.data.name);
        $('#form-booking>input[name="email"]').val(res.data.email);

        $("#loginModal").modal("hide");
        $("#bookingModal").modal("show");
    } else {
        alert("NIS atau Password salah. Silakan coba lagi.");
    }
}

function clearForms(){
    $('#form-login')[0].reset();
    $('#form-booking')[0].reset();
}

setInterval(updateDateTime, 1000);
setInterval(updateBookings, 5000);
