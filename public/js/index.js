document.addEventListener("DOMContentLoaded", function () {
    const checkinInput = document.getElementById("location-input-checkin");
    const checkoutInput = document.getElementById("location-input-checkout");
    const targetLatitude = -7.5450414;
    const targetLongitude = 111.6599448;
    const targetLocationName = "Unico Cafe, Caruban, Kab Madiun, Jawa Timur";
    const proximityRadiusMeters = 100;

    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * (Math.PI / 180);
        const dLon = (lon2 - lon1) * (Math.PI / 180);
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * (Math.PI / 180)) *
                Math.cos(lat2 * (Math.PI / 180)) *
                Math.sin(dLon / 2) *
                Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const distance = R * c;
        return distance;
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const apiKey =
                    document.getElementById("location-api-key").value;
                const url = `https://us1.locationiq.com/v1/reverse.php?key=${apiKey}&lat=${latitude}&lon=${longitude}&format=json`;

                fetch(url)
                    .then((response) => response.json())
                    .then((data) => {
                        let locationName = data.display_name;

                        const distance = calculateDistance(
                            latitude,
                            longitude,
                            targetLatitude,
                            targetLongitude
                        );

                        if (distance <= proximityRadiusMeters) {
                            locationName = targetLocationName;
                        }
                        if (checkinInput) {
                            checkinInput.value = locationName;
                        }
                        if (checkoutInput) {
                            checkoutInput.value = locationName;
                        }
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

    let timerInterval;

    // Fungsi untuk memformat waktu menjadi hh:mm:ss
    function formatTime(seconds) {
        const hours = String(Math.floor(seconds / 3600)).padStart(2, "0");
        const minutes = String(Math.floor((seconds % 3600) / 60)).padStart(
            2,
            "0"
        );
        const secs = String(seconds % 60).padStart(2, "0");
        return `${hours}:${minutes}:${secs}`;
    }

    // Fungsi untuk memulai timer
    function startTimer(checkinTime) {
        timerInterval = setInterval(() => {
            const now = new Date();
            const elapsedTime = Math.floor(
                (now - new Date(checkinTime)) / 1000
            );
            document.getElementById("checkin-time").innerText =
                formatTime(elapsedTime);
        }, 1000);
    }
    const checkinTimeInput =
        document.getElementById("checkin-time-input").value;
    console.log(checkinTimeInput);
    if (checkinTimeInput) {
        startTimer(checkinTimeInput);
    }
    Webcam.set({
        width: 490,
        height: 350,
        image_format: "jpeg",
        jpeg_quality: 90,
    });

    Webcam.attach("#my_camera");

    function take_snapshot() {
        Webcam.snap(function (data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById("results").innerHTML =
                '<img src="' + data_uri + '"/>';
        });
    }
});
