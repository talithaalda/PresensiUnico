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

                        // Menyimpan nama lokasi ke dalam input tersembunyi
                        document.getElementById("location-input").value =
                            locationName;
                        document.getElementById(
                            "location"
                        ).innerText = `Location: ${locationName}`;
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
    
});
