<?php

use App\Http\Controllers\ColocationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return redirect()->route('colocations.index');
})->middleware(['auth'])->name('dashboard');

Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])->middleware('auth')->name('invitations.accept');

Route::middleware(['auth', 'banned'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('colocations', ColocationController::class);
    Route::post('/colocations/{colocation}/invite', [InvitationController::class, 'send'])->name('colocations.invite');
    Route::post('/colocations/{colocation}/leave', [ColocationController::class, 'leave'])->name('colocations.leave');
    Route::delete('/colocations/{colocation}/members/{member}', [ColocationController::class, 'removeMember'])->name('colocations.removeMember');
    
    Route::post('/colocations/{colocation}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/colocations/{colocation}/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/colocations/{colocation}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
});

require __DIR__.'/auth.php';
