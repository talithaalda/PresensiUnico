document.addEventListener("DOMContentLoaded", function () {
    const checkinInput = document.getElementById("location-input-checkin");
    const checkoutInput = document.getElementById("location-input-checkout");

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const apiKey = "pk.76953e33cfec67c6c2d77d171a27336c"; // Ganti dengan API Key Anda
                const url = `https://us1.locationiq.com/v1/reverse.php?key=${apiKey}&lat=${latitude}&lon=${longitude}&format=json`;

                fetch(url)
                    .then((response) => response.json())
                    .then((data) => {
                        const locationName = data.display_name;

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
