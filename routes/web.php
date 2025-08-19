<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PetInventoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
    Route::get('/common-diseases', [DashboardController::class, 'getCommonDiseasesData']);
    Route::get('/monthly-clients', [DashboardController::class, 'getMonthlyClientsData']);
    Route::get('/monthly-sales', [DashboardController::class, 'getMonthlySalesData']);


    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

    Route::get('/registered', [PetInventoryController::class, 'showPatients'])->name('registered');
    Route::post('/registered/update', [PetInventoryController::class, 'updateMedicalInfo'])->name('registered.update');
    Route::post('/registered/checkup', [PetInventoryController::class, 'storeCheckup'])->name('registered.checkup.store');


    Route::get('/registerpet', function () {
        return view('registerpet');
    })->name('registerpet');
    Route::post('/registerpet/store', [PetInventoryController::class, 'store'])->name('registerpet.store');

    Route::get('/schedule', function () {
        return view('schedule');
    });

    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
    Route::post('/schedule', [ScheduleController::class, 'store'])->name('schedule.store');

    Route::post('/complete-purchase', [TransactionController::class, 'completePurchase']);

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');
    
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
