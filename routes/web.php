<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PetInventoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OtpController;

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

Route::get('/api/transactions/daily', function () {
    $transactions = \App\Models\Transaction::with('items')
        ->whereDate('created_at', today())
        ->get();

    return response()->json(['transactions' => $transactions]);
});

Route::get('/api/transactions/weekly', function () {
    $transactions = \App\Models\Transaction::with('items')
        ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
        ->get();

    return response()->json(['transactions' => $transactions]);
});

// OTP request
Route::get('/otp-request', [OtpController::class, 'showEmailForm'])->name('otp.request');
Route::post('/otp-send', [OtpController::class, 'sendOtp'])->name('otp.send');
Route::get('/otp-verify', [OtpController::class, 'showOtpForm'])->name('otp.verify.form');
Route::post('/otp-verify', [OtpController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/otp/check', [OtpController::class, 'verifyOtp'])->name('otp.check');

// Protected Profile
Route::get('/profile', function () {
    if (!session()->get('otp_verified')) {
        return redirect()->route('otp.request');
    }
    return view('profile.edit');
})->name('profile.edit');


require __DIR__ . '/auth.php';
