<?php

use App\Http\Controllers\QrScannerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

// QR Scanner routes (auth protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/scanner', [QrScannerController::class, 'index'])->name('scanner');
    Route::post('/scanner/lookup', [QrScannerController::class, 'lookup'])->name('scanner.lookup');
    Route::get('/equipment/{equipment}', [QrScannerController::class, 'show'])->name('equipment.show');
});