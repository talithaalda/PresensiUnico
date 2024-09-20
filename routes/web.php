<?php

use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('/admin', function () {
//     // ...
// })->middleware(AdminMiddleware::class);
Route::post('/presensi', [PresensiController::class, 'store'])->name('presensi.store');
Route::put('/presensi/{id}', [PresensiController::class, 'update'])->name('presensi.update');
