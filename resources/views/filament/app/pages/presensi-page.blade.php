<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<script src="{{ asset('js/index.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<x-filament-panels::page>
    <div class="container-attend">
        <div>
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

                    </div>
                </form>
            @endif
        </div>
        <div>
            @if ($user->checkedIn == false || $presensi?->status == 'pulang')
                <button type="submit" class="button-attend button-checkin mt-6">
                    <b>Hadir</b>
                </button>
            @elseif ($user->checkedIn == true || $presensi?->status == 'hadir')
                <button type="submit" class="button-attend button-checkout mt-6">
                    <b>Pulang</b>
                </button>
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
    Webcam.set({
        image_format: "jpeg",
        jpeg_quality: 90,
        constraints: {
            video: {
                facingMode: {
                    exact: "environment"
                }
            }
        }
    });


    Webcam.attach("#my_camera");

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
