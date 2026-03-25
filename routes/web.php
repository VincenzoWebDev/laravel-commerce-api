<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'auth.login')->name('ui.login');
Route::view('/logout', 'auth.logout')->name('ui.logout');
Route::view('/products', 'products.index')->name('ui.products.index');
Route::view('/products/new', 'products.create')->name('ui.products.create');
Route::view('/orders', 'orders.index')->name('ui.orders.index');
Route::view('/orders/new', 'orders.create')->name('ui.orders.create');
Route::view('/orders/{id}', 'orders.show')->name('ui.orders.show');
