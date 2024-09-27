<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<script src="{{ asset('js/index.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/js/webcamjs/webcam.js"></script>
<x-filament-panels::page>
    <div class="container-attend">
        <div class="flex flex-col items-center gap-0">
            <h1 class="text-3xl font-bold hello">Hello, {{ ucfirst(Auth::user()->name) }}!</h1>
            @if ($user->checkedIn == true || $presensi?->status == 'hadir')
                <div class="mb-4">
                    <span id="checkin-time" class="checkin-time"></span>
                </div>
            @endif
        </div>


        <div>
            <div class="flex flex-col items-center gap-0 mb-2">
                <x-filament::badge icon="ionicon-location" class="location ">
                    <span id="location">Memuat lokasi...</span>
                </x-filament::badge>
                @error('location')
                    <div class=" text-sm ">Lokasi tidak ditemukan!</div>
                @enderror
            </div>
            @if ($user->checkedIn == false || $presensi?->status == 'pulang')
                <form action="{{ route('presensi.store') }}" method="POST" id="checkin-form">
                    @csrf
                    <div class="flex flex-col items-center gap-0">
                        <div id="my_camera" class="camera"></div>
                        <div id="results" class="camera" style="display: none"></div>
                        <button onclick="toggleCamera()">Ganti Kamera</button>

                        <x-filament::button class="font-bold button-snap button-camera" id="button-snap" type="button"
                            onClick="take_snapshot()">
                            <x-ionicon-camera-sharp class="w-6 h-6" />
                        </x-filament::button>
                        <x-filament::button class="font-bold button-reset mt-3 button-camera" type="button"
                            id="retake_button" onClick="retake_snapshot()" style="display:none;">
                            <b>Foto Ulang</b>
                        </x-filament::button>
                        <input type="hidden" name="image_datang" class="image-tag">
                        @error('image_datang')
                            <div class="text-sm text-red-500 py-3" style="color: red">
                                <span>Please take a picture!</span>
                                <br />
                            </div>
                        @enderror
                        {{-- test --}}
                        <input type="hidden" name="status" value="hadir">
                        <input type="hidden" name="location" id="location-input-checkin">
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        <button type="submit" class="button-attend button-checkin mt-3">
                            <b>Hadir</b>
                        </button>
                    </div>
                </form>
            @elseif ($user->checkedIn == true || $presensi?->status == 'hadir')
                <input type="hidden" id="checkin-time-input" value="{{ $presensi->created_at }}">
                <form method="POST" action="{{ route('presensi.update', $presensiId) }}" id="checkout-form">
                    @csrf
                    @method('PUT')
                    <div class="flex flex-col items-center gap-0">
                        <div id="my_camera" class="camera"></div>
                        <div id="results" class="camera" style="display: none"></div>
                        <x-filament::button class="font-bold button-snap" id="button-snap" type="button"
                            onClick="take_snapshot()">
                            <x-ionicon-camera-sharp class="w-6 h-6" />
                        </x-filament::button>
                        <x-filament::button class="font-bold button-reset mt-3" type="button" id="retake_button"
                            onClick="retake_snapshot()" style="display:none;">
                            <b>Foto Ulang</b>
                        </x-filament::button>
                        <input type="hidden" name="image_pulang" class="image-tag">
                        @error('image_pulang')
                            <div class="text-sm text-red-500 dark:text-gray-400 py-3" style="color: red">
                                <span>Please take a picture!</span>
                                <br />
                            </div>
                        @enderror
                        <input type="hidden" name="location" id="location-input-checkout">
                        <button type="submit" class="button-attend button-checkout mt-3">
                            <b>Pulang</b>
                        </button>
                    </div>
                </form>
            @endif
        </div>

        @if (session('success'))
            <div
                class="container-success justify-center fixed p-6 rounded-xl shadow-xl z-50 text-center
                bg-white text-gray-800 dark:bg-gray-800 dark:text-white border border-gray-300 dark:border-gray-700
                animate-bounce-in transition duration-300 ease-in-out">
                <div class="flex justify-center mb-4">
                    <div class="icon">
                        {{ svg('heroicon-o-check-circle') }}
                    </div>
                </div>
                <p class="success">{{ session('success') }}</p>
                <div class="button-ok">
                    <x-filament::button onclick="this.closest('.container-success').style.display='none';">
                        OK
                    </x-filament::button>
                </div>
            </div>
        @endif
        <input type="hidden" id="location-api-key" value="{{ env('LOCATIONIQ_API_KEY') }}">
    </div>
</x-filament-panels::page>
<script>
    var cameras = []; // Buat array kosong untuk menyimpan perangkat video yang tersedia

    // Fungsi untuk mendapatkan dan menyimpan deviceId dari kamera yang ditemukan
    function getCameras() {
        navigator.mediaDevices.enumerateDevices() // Ambil perangkat yang tersedia
            .then(function(devices) {
                var i = 0;
                devices.forEach(function(device) {
                    if (device.kind === "videoinput") { // Filter hanya perangkat video
                        cameras[i] = device.deviceId; // Simpan deviceId kamera dalam array
                        i++;
                    }
                });

                // Periksa apakah ada kamera yang terdeteksi
                if (cameras.length > 0) {
                    initializeCamera(0); // Inisialisasi kamera pertama (0 untuk depan, 1 untuk belakang)
                } else {
                    console.error("Tidak ada kamera yang ditemukan.");
                }
            })
            .catch(function(err) {
                console.error("Error mendapatkan perangkat kamera: ", err);
            });
    }

    // Fungsi untuk menginisialisasi kamera berdasarkan deviceId
    function initializeCamera(cameraIndex) {
        if (cameras[cameraIndex]) {
            Webcam.set({
                width: 500,
                height: 350,
                image_format: 'jpeg',
                jpeg_quality: 90,
                constraints: {
                    video: {
                        deviceId: {
                            exact: cameras[cameraIndex]
                        } // Set deviceId kamera yang dipilih
                    }
                }
            });
            Webcam.attach('#my_camera'); // Pasang kamera di elemen dengan id 'my_camera'
        } else {
            console.error("Kamera dengan index tersebut tidak ditemukan.");
        }
    }

    // Fungsi untuk mengganti antara kamera depan dan belakang
    function toggleCamera() {
        if (cameras.length >= 2) { // Pastikan ada lebih dari satu kamera
            Webcam.reset(); // Reset webcam sebelum mengganti kamera
            var currentCameraIndex = cameras.indexOf(Webcam.params.constraints.video.deviceId.exact);
            var nextCameraIndex = currentCameraIndex === 0 ? 1 : 0; // Toggle antara kamera depan dan belakang
            initializeCamera(nextCameraIndex); // Inisialisasi kamera baru
        } else {
            console.error("Hanya satu kamera yang terdeteksi.");
        }
    }

    // Panggil fungsi ini saat halaman dimuat untuk mendapatkan kamera
    window.onload = function() {
        getCameras(); // Dapatkan daftar kamera saat halaman dimuat
    };

    function take_snapshot() {
        console.log("Tombol snapshot ditekan");
        Webcam.snap(function(data_uri) {
            console.log("Snapshot diambil");
            $(".image-tag").val(data_uri);
            document.getElementById("results").innerHTML =
                '<img src="' + data_uri + '"/>';
            document.getElementById("results").style.display = "block";
            document.getElementById("my_camera").style.display = "none";
            document.getElementById("button-snap").style.display = "none";
            document.getElementById("retake_button").style.display = "block";

        });
    }

    function retake_snapshot() {
        console.log("Foto ulang ditekan");
        $(".image-tag").val(null);
        document.getElementById("results").style.display = "none"; // Sembunyikan hasil snapshot
        document.getElementById("my_camera").style.display = "block"; // Tampilkan kamera kembali
        document.getElementById("retake_button").style.display = "none";
        document.getElementById("button-snap").style.display = "block";
    }
</script>
