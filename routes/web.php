<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QrScannerController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes (no guest middleware — avoids redirect loop with Filament)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// QR Scanner routes (auth protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/scanner', [QrScannerController::class, 'index'])->name('scanner');
    Route::post('/scanner/lookup', [QrScannerController::class, 'lookup'])->name('scanner.lookup');
    Route::get('/equipment/{equipment}', [QrScannerController::class, 'show'])->name('equipment.show');
});