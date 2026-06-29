<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'admin_or_manager'])->group(function () {

    // Bottles
    Route::get('/bottles/create', fn() =>
        Inertia::render('Bottles/Create')
    )->name('bottles.create');

    Route::get('/bottles/transactions', fn() =>
        Inertia::render('Bottles/Transactions')
    )->name('bottles.transactions');

    // Packing Materials
    Route::get('/packing-materials/create', fn() =>
        Inertia::render('PackingMaterials/Create')
    )->name('packing-materials.create');

    Route::get('/packing-materials/transactions', fn() =>
        Inertia::render('PackingMaterials/Transactions')
    )->name('packing-materials.transactions');

    // Chemicals
    Route::get('/chemicals/create', fn() =>
        Inertia::render('Chemicals/Create')
    )->name('chemicals.create');

    Route::get('/chemicals/transactions', fn() =>
        Inertia::render('Chemicals/Transactions')
    )->name('chemicals.transactions');
});

Route::middleware(['auth', 'manager'])->group(function () {
    Route::get('/users', fn () =>
        Inertia::render('Users/Index')
    )->name('users.index');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', fn() =>
    Inertia::render('Dashboard/Index')
    )->name('dashboard');

    Route::get('/chemicals', fn() =>
        Inertia::render('Chemicals/Index')
    )->name('chemicals.index');

    Route::get('/packing-materials', fn() =>
        Inertia::render('PackingMaterials/Index')
    )->name('packing-materials.index');

    Route::get('/bottles', fn() =>
        Inertia::render('Bottles/Index')
    )->name('bottles.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});


require __DIR__.'/auth.php';
