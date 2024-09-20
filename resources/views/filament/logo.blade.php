@php
    $isDarkMode = local('theme') === 'dark';
@endphp

@if ($isDarkMode)
    <img src="{{ asset('images/logo-name-dark.png') }}" alt="Logo">
@if
    <img src="{{ asset('images/logo-name.png') }}" alt="Logo">
@endif
