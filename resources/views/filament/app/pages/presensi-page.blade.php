<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/sweetalert2.css') }}">
<script src="{{ asset('js/filament/filament/location.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<x-filament-panels::page>

    <div class="container-attend">
        <h1 class="text-3xl font-bold hello">Hello, {{ ucfirst(Auth::user()->name) }}!</h1>
        <form action="{{ route('presensi.store') }}" method="POST" id="presensi-form">
            @csrf
            <input type="hidden" id="location-input" name="location">
            <input type="hidden" name="status" value="hadir">
            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
            <x-filament::button type="submit" class="button-attend">
                Hadir
            </x-filament::button>
        </form>


        <x-filament::badge icon="ionicon-location" class="location">
            <span id="location">Memuat lokasi...</span>
        </x-filament::badge>
    </div>
    @if (session('success'))
        <div x-data="{ show: true }" :style="show ? 'display: flex;' : 'display: none;'"
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
                <x-filament::button @click="show = false">
                    OK
                </x-filament::button>
            </div>
        </div>
    @endif

</x-filament-panels::page>
