<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// La autenticación se manejará con JavaScript y las APIs
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');
