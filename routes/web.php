<?php

use DefStudio\Burnout\Controllers\BurnoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('burnout', [BurnoutController::class, 'index'])->name('burnout.index');
    Route::get('burnout/{burnout_entry}', [BurnoutController::class, 'show'])->name('burnout.show');
    Route::delete('burnout', [BurnoutController::class, 'clear'])->name('burnout.clear');
    Route::delete('burnout/{burnout_entry}', [BurnoutController::class, 'destroy'])->name('burnout.destroy');
});



