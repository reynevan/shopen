<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Shopen\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use Shopen\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use Shopen\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use Shopen\Http\Controllers\Admin\Auth\PasswordController;
use Shopen\Http\Controllers\Admin\Auth\VerifyEmailController;

use Shopen\Http\Controllers\Frontend\Auth\LoginController;
use Shopen\Http\Controllers\Frontend\Auth\OAuthController;
use Shopen\Http\Controllers\Frontend\Auth\PasswordResetLinkController;
use Shopen\Http\Controllers\Frontend\Auth\RegisterController;
use Shopen\Http\Controllers\Frontend\Auth\NewPasswordController;

Route::middleware(['guest', 'web'])->group(function () {

    Route::get('logowanie', [LoginController::class, 'create'])->name('login');

    Route::post('logowanie', [LoginController::class, 'store']);

    Route::get('rejestracja', [RegisterController::class, 'create'])->name('sign-up');

    Route::post('rejestracja', [RegisterController::class, 'store']);

    Route::get('odzyskiwanie-hasla', [PasswordResetLinkController::class, 'create'])->name('password.remind');

    Route::post('odzyskiwanie-hasla', [PasswordResetLinkController::class, 'store'])
        ->middleware(['throttle:5,1'])
        ->name('password.email');

    Route::get('resetowanie-hasla/{token}', [NewPasswordController::class, 'create'])->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');

    Route::get('/oauth/redirect/google', function () {
        return Socialite::driver('google')->redirect();
    })->name('oauth.google.redirect');

    Route::get('/oauth/redirect/facebook', function () {
        return Socialite::driver('facebook')->redirect();
    })->name('oauth.facebook.redirect');

    Route::get('/oauth/callback/{provider}', [OAuthController::class, 'callback']);
});

Route::middleware(['auth', 'web'])->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [LoginController::class, 'destroy'])
        ->name('logout');
});
