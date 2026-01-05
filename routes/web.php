<?php

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;
use Lunar\Models\Order;

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
Route::view('/legal', 'legal')
    ->name('legal');
Route::view('/terms', 'terms')
    ->name('terms');

Route::get('/invoice/{ref}', function ($ref) {
    $order = Order::firstWhere('reference', $ref);

    if(!$order) {
        abort(404);
    }

    return Pdf::loadView('lunarpanel::pdf.order', [
        'record' => $order,
    ])->stream();
})->name('invoice');

Route::middleware(['auth', 'disabled'])->group(function () {
    Route::view('/account', 'account')
        ->name('account');

    Route::view('/account/edit', 'account-edit')
        ->name('account.edit');
});

require __DIR__.'/auth.php';
