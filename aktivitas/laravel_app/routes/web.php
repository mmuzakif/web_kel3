<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;

Route::get('/', [InvitationController::class, 'index'])->name('invitation.index');
Route::post('/rsvp', [InvitationController::class, 'store'])->name('rsvp.store');
Route::redirect('/undangan.php', '/');
