<?php

use App\Http\Controllers\PresensiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/admin', function () {
//     // ...
// })->middleware(AdminMiddleware::class);
Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');
Route::put('/presensi/{id}', [PresensiController::class, 'update'])->name('presensi.update');
Route::middleware(['auth'])->group(function () {
    Route::get('/delete-account', [UserController::class, 'deleteAccount'])->name('delete-account');
});
