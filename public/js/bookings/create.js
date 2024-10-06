let bookingsData = [];

$(document).ready(() => {
    generateCalendar();
    updateDateTime();
    updateRooms();

    $("#select-room").select2({
        dropdownParent: $("#bookingModal"),
        width: "resolve",
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
            $('input[name="date"]').val(info.dateStr);
            $("#loginModal").modal("show");
        },
    });
    calendar.render();
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
                <span>Dipinjam oleh ${booking.nama} dari ${formatTime(booking.start_time)} hingga ${formatTime(booking.end_time)}</span>
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
