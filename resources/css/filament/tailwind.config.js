import preset from "../../../../vendor/filament/filament/tailwind.config.preset";

export default {
    presets: [preset],
    darkMode: "class",
    content: [
        "./app/Filament/App/**/*.php",
        "./resources/views/filament/app/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
        "./resources/**/*.blade.php",
    ],
};
