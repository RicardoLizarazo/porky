<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Livewire\Reports\ReportsModule;

// Página inicial → login
Route::get('/', function () {
    return view('auth.login');
})->name('root');

Route::prefix('customer')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])
        ->name('customer.login');

    Route::post('/login', [CustomerAuthController::class, 'login'])
        ->name('customer.login.post');

    Route::post('/logout', [CustomerAuthController::class, 'logout'])
        ->name('customer.logout');
});

Route::middleware(['auth.any'])->group(function () {
    Route::get('/menu', fn() => view('livewire.menu'))->name('menu');
});

Route::post('/logout-any', function (Request $request) {

    $redirect = '/';

    if (Auth::guard('customer')->check()) {
        Auth::guard('customer')->logout();
        $redirect = route('customer.login');
    }

    elseif (Auth::check()) {
        Auth::logout();
        $redirect = '/login';
    }

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect($redirect);

})->name('logout.any');

Auth::routes();

Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
Route::get('/orders/{order}/invoice-pdf', [OrderController::class, 'invoicePdf'])->name('orders.invoice.pdf');
Route::get('/orders/{order}/ticket', [OrderController::class, 'ticket'])->name('orders.ticket');

// Rutas protegidas (solo si hay sesión web activa)
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', fn() => view('livewire.dashboard'))->name('dashboard');
    Route::get('/orders', fn() => view('livewire.orders'))->name('orders');
    Route::get('/customers', fn() => view('livewire.customers'))->name('customers');
    Route::get('/locations', fn() => view('livewire.locations'))->name('locations');
    Route::get('/products', fn() => view('livewire.products'))->name('products');
    Route::get('/categories', fn() => view('livewire.categories'))->name('categories');
    Route::get('/users', fn() => view('livewire.users'))->name('users');
    Route::get('/roles', fn() => view('livewire.roles'))->name('roles');
    Route::get('/permissions', fn() => view('livewire.permissions'))->name('permissions');
    Route::get('/audit-logs', fn() => view('livewire.audit-logs'))->name('audit-logs');

    // 🔥 REPORTES
    Route::get('/reports', fn() => view('reports'))->name('reports');
    Route::get('/reports/{type}', function ($type) {
        request()->merge(['type' => $type]);
        return view('reports');
    })->name('reports.type');

    Route::get('/routes', function () {return view('routes.index');})->name('routes.index');
});
