<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Rutas protegidas (solo si hay sesión web activa)
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    //Route::get('/dashboard', fn() => view('livewire.dashboard'))->name('dashboard');
    Route::get('/locations', fn() => view('livewire.locations'))->name('locations');
    Route::get('/products', fn() => view('livewire.products'))->name('products');
    Route::get('/categories', fn() => view('livewire.categories'))->name('categories');
    Route::get('/users', fn() => view('livewire.users'))->name('users');
    Route::get('/roles', fn() => view('livewire.roles'))->name('roles');
    Route::get('/permissions', fn() => view('livewire.permissions'))->name('permissions');
    Route::get('/audit-logs', fn() => view('livewire.audit-logs'))->name('audit-logs');
});
