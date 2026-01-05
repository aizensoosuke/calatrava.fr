<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'home')
    ->name('home');
Route::view('/collection/{slug}', 'collection')
    ->name('collection');
Route::view('/checkout', 'checkout')
    ->name('checkout');
Route::view('/payment-callback', 'payment-callback')
    ->name('payment-callback');
Route::view('/order/{ref}', 'order')
    ->name('order');

Route::middleware(['auth', 'disabled'])->group(function () {
    Route::view('/account', 'account')
        ->name('account');

    Route::view('/account/edit', 'account-edit')
        ->name('account.edit');
});

require __DIR__.'/auth.php';
