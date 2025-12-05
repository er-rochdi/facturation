<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/invoices/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'streamPdf'])->name('invoices.pdf');
