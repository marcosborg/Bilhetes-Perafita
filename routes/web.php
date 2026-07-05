<?php

use App\Http\Controllers\GroupPortalController;
use App\Http\Controllers\MagicLoginController;
use App\Http\Controllers\TicketDownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/grupo/{group:public_token}', [GroupPortalController::class, 'show'])
    ->name('groups.portal');
Route::post('/grupo/{group:public_token}/bilhete/{ticket}/enviado', [GroupPortalController::class, 'markSent'])
    ->name('groups.tickets.sent');
Route::get('/bilhete/{token}', [TicketDownloadController::class, 'show'])
    ->name('tickets.download');
Route::get('/entrar/{user}/{token}', [MagicLoginController::class, 'redirect'])
    ->name('magic-login');
Route::get('/acesso/{user}/{token}', [MagicLoginController::class, 'show'])
    ->name('magic-portal');
Route::post('/acesso/{user}/{token}/bilhete/{ticket}/enviado', [MagicLoginController::class, 'markSent'])
    ->name('magic-portal.tickets.sent');
