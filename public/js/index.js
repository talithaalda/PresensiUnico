document.addEventListener("DOMContentLoaded", function () {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const url = `https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json&accept-language=en`;

                fetch(url)
                    .then((response) => response.json())
                    .then((data) => {
                        const locationName = data.display_name;
                        document.getElementById("location-input").value =
                            locationName;
                        document.getElementById(
                            "location-input-checkout"
                        ).value = locationName;
                        document.getElementById(
                            "location"
                        ).innerText = ` ${locationName}`;
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        document.getElementById("location").innerText =
                            "Unable to retrieve location.";
                    });
            },
            function (error) {
                console.error("Geolocation error:", error);
                const errorMessage =
                    error.code === error.PERMISSION_DENIED
                        ? "Geolocation permission denied."
                        : "Unable to retrieve location.";
                document.getElementById("location").innerText = errorMessage;
            }
        );
    } else {
        document.getElementById("location").innerText =
            "Geolocation is not supported by this browser.";
    }

    const isCheckIn = localStorage.getItem("checkedIn");
    const presensiId = localStorage.getItem("presensi_id");
    const startTime = localStorage.getItem("startTime");

    if (isCheckIn === "true" && presensiId) {
        console.log("masuk");
        document.getElementById("checkout-form").style.display = "block";
        document.getElementById("checkin-form").style.display = "none";
        const form = document.getElementById("checkout-form");
        form.action = "presensi/" + presensiId;
    } else if (isCheckIn === "false") {
        document.getElementById("checkin-form").style.display = "block";
    } else {
        document.getElementById("checkin-form").style.display = "block"; // Tampilkan form check-in jika tidak ada data
        document.getElementById("checkout-form").style.display = "none"; // Sembunyikan form check-out
    }

    function updateCheckinTime() {
        const checkinTimeElement = document.getElementById("checkin-time");
        if (isCheckIn && startTime) {
            const currentTime = new Date().getTime();
            const diffInSeconds = Math.floor((currentTime - startTime) / 1000);

            // Calculate hours, minutes, and seconds
            const hours = Math.floor(diffInSeconds / 3600);
            const minutes = Math.floor((diffInSeconds % 3600) / 60);
            const seconds = diffInSeconds % 60;

            // Helper function to add leading zero
            const formatTime = (time) => (time < 10 ? `0${time}` : time);

            // Construct time string with leading zeros
            let timeString = "";
            timeString += `${formatTime(hours)} : `;
            timeString += `${formatTime(minutes)} : `;
            timeString += `${formatTime(seconds)}`;
            checkinTimeElement.innerHTML = `<span class="timer">${timeString}</span>`;
        } else {
            checkinTimeElement.innerText = "";
        }
    }

    // Initial update
    updateCheckinTime();

    // Update every second
    setInterval(updateCheckinTime, 1000);
});
