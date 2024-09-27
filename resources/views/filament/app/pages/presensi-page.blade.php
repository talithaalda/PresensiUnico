<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<script src="{{ asset('js/index.js') }}"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.js"></script>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
{{-- <script src="/js/webcamjs/webcam.js"></script> --}}
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
                        <div class="w-full flex justify-end justify-items-end pe-6">
                            <input class=" p-4 my-2 rounded-md sm:block md:block lg:hidden z-30"
                                style="margin-top: -50px" type="button" id="btnFrontBack" value="Back" />
                        </div>
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
                        <div class="w-full flex justify-end justify-items-end pe-6">
                            <input class=" p-4 my-2 rounded-md sm:block md:block lg:hidden z-30"
                                style="margin-top: -50px" type="button" id="btnFrontBack" value="Back" />
                        </div>
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
<script type="text/javascript">
    function setCameraDimensions() {
        var mode = $('#btnFrontBack').val() == 'Back' ? 'environment' : 'user'; // Menentukan kamera
        if (window.innerWidth <= 480) {
            Webcam.set({
                width: 320, // Untuk layar kecil (mobile)
                height: 240,
                image_format: 'jpeg',
                jpeg_quality: 90,
                constraints: {
                    facingMode: mode // Atur mode kamera depan atau belakang

                }
            });
        } else if (window.innerWidth <= 768) {
            Webcam.set({
                width: 480, // Untuk layar tablet
                height: 320,
                image_format: 'jpeg',
                jpeg_quality: 90,
                constraints: {
                    facingMode: mode
                }
            });
        } else {
            Webcam.set({
                width: 520, // Untuk layar besar (desktop)
                height: 350,
                image_format: 'jpeg',
                jpeg_quality: 90,
            });
        }
        Webcam.attach('#my_camera');
    }

    // Panggil fungsi ini saat halaman dimuat
    setCameraDimensions();

    // Tambahkan event listener untuk merespons perubahan ukuran layar
    window.addEventListener('resize', setCameraDimensions);

    // Fungsi untuk mengambil snapshot
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

    // Fungsi untuk mengambil ulang snapshot
    function retake_snapshot() {
        console.log("Foto ulang ditekan");
        $(".image-tag").val(null);
        document.getElementById("results").style.display = "none"; // Sembunyikan hasil snapshot
        document.getElementById("my_camera").style.display = "block"; // Tampilkan kamera kembali
        document.getElementById("retake_button").style.display = "none";
        document.getElementById("button-snap").style.display = "block";
    }

    // Toggle untuk beralih antara kamera depan dan belakang
    $("#btnFrontBack").click(function() {
        $('#btnFrontBack').val($('#btnFrontBack').val() == 'Back' ? 'Front' : 'Back');
        Webcam.reset(); // Reset webcam sebelum mengganti mode kamera
        setCameraDimensions(); // Terapkan dimensi kamera ulang sesuai pilihan mode
    });
</script>
