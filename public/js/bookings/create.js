let bookingsData = [];

$(document).ready(() => {
    generateCalendar();
    updateDateTime();
    updateRooms();

    $("#select-room").select2({
        dropdownParent: $("#bookingModal"),
        width: "resolve",
    });
    
    $('#form-booking').submit(async e => {
        e.preventDefault();
        const formData = new FormData(e.currentTarget);
        if(isTimeLess(formData.get("end_time"), formData.get("start_time"))){
            alert("Jam Selesai tidak bisa kurang dari Jam Mulai.");
            e.preventDefault();
            return;
        }
        
        const rooms = await $.get(roomListUrl);
        let bookings = rooms.filter(dat => dat.id === parseInt(formData.get('room_id')))[0].bookings;
        if(bookings.length > 0){
            const bookingsToday = bookings.filter(dat => isToday(dat.date));
            if(bookingsToday.some(dat => isTimeRangeOverlap(formData.get("start_time"), formData.get("end_time"), formatTime(dat.start_time), formatTime(dat.end_time)))){
                alert("Jam peminjaman sudah digunakan oleh user lain.");
                return;
            }
        }
        await $.post($('#form-booking').attr('action'), $('#form-booking').serialize());
        location.reload();
    });
});

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
            if (isAtLeastOneDayLess(info.date, new Date())) {
                alert("Tidak bisa memilih hari sebelumnya.");
                return;
            }
            $('input[name="date"]').val(info.dateStr);
            $("#loginModal").modal("show");
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

async function updateRooms() {
    bookingsData = await $.get(listUrl);

    const currentBookingsDiv = $("#current-bookings");
    currentBookingsDiv.html("<h4>Jam Penggunaan Hari Ini:</h4>");

    if (bookingsData.length === 0) {
        currentBookingsDiv.append("<p>Tidak ada peminjaman hari ini.</p>");
    } else {
        bookingsData.forEach((booking, index) => {
            currentBookingsDiv.append(`
            <div>
                <span>${booking.room.name} dipinjam oleh ${booking.nama} dari ${formatTime(booking.start_time)} hingga ${formatTime(booking.end_time)}</span>
                ${isAuth ? `<a href="${destroyUrl}?id=${booking.id}"><button class="btn btn-danger btn-sm ml-2">Hapus</button></a>` : ""}
            </div>
            `);
        });
    }
}

function formatTime(time) {
    return time.split(":").slice(0, -1).join(":");
}

async function checkLogin() {
    const nis = document.getElementById("login-nis").value;
    const password = document.getElementById("login-password").value;

    const res = await $.post(loginUrl, { nis, password });
    if (res.success) {
        $('input[name="nis"]').val(nis);
        $('input[name="password"]').val(password);

        $("#loginModal").modal("hide");
        $("#bookingModal").modal("show");
    } else {
        alert("NIS atau Password salah. Silakan coba lagi.");
    }
}

setInterval(updateDateTime, 1000);
setInterval(updateRooms, 5000);
