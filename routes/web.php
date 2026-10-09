<?php

use App\Http\Controllers\ComboController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ComboController::class, 'index'])->name('combos.index');
Route::post('/combos', [ComboController::class, 'generar'])->name('combos.generar');
Route::get('/combos/{combo}', [ComboController::class, 'show'])->name('combos.show');
Route::get('/historial', [ComboController::class, 'historial'])->name('combos.historial');