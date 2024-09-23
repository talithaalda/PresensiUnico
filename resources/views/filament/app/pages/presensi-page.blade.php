<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<script src="{{ asset('js/index.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<x-filament-panels::page>
    <div class="container-attend">
        <h1 class="text-3xl font-bold hello">Hello, {{ ucfirst(Auth::user()->name) }}!</h1>
        @if ($user->checkedIn == true || $presensi?->status == 'hadir')
            <div class="mb-4">
                <span id="checkin-time" class="checkin-time"></span>
            </div>
        @endif
        @if ($user->checkedIn == false || $presensi?->status == 'pulang')
            <form action="{{ route('presensi.store') }}" method="POST" id="checkin-form">
                @csrf
                <input type="hidden" name="status" value="hadir">
                <input type="hidden" name="location" id="location-input-checkin">

                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                <button type="submit" class="button-attend button-checkin">
                    <b>Hadir</b>
                </button>
            </form>
        @elseif ($user->checkedIn == true || $presensi?->status == 'hadir')
            <input type="hidden" id="checkin-time-input" value="{{ $presensi->created_at }}">
            <form method="POST" action="{{ route('presensi.update', $presensiId) }}" id="checkout-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="location" id="location-input-checkout">
                <button type="submit" class="button-attend button-checkout">
                    <b>Pulang</b>
                </button>
            </form>
        @endif

        <x-filament::badge icon="ionicon-location" class="location">
            <span id="location">Memuat lokasi...</span>
        </x-filament::badge>
        @error('location')
            <div class=" text-sm mb-2">Lokasi tidak ditemukan!</div>
        @enderror

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
