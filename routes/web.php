<?php

namespace App\Http\Controllers;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Http\Controllers\SendInvoice;
use App\Http\Controllers\LocalityOptionController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\MailController;

use stdClass;

Route::get('/', function () {
    return redirect()->route(Auth::check() ? 'dashboard' : 'login');
})->name('index');

Route::get('/create_invoice', function () {
    return view('create_invoice');
})->name('create_invoice_form');
Route::get('/get-localities', [LocalityOptionController::class, 'getLocalities']);
Route::post('/store-locality', [LocalityOptionController::class, 'storeLocality']);
Route::post('/create_invoice', 'App\Http\Controllers\InvoiceController@createInvoice')->name('create_invoice');

Route::get('/edit_invoice/{id}', 'App\Http\Controllers\InvoiceController@editInvoice')->name('edit_invoice_form');

Route::post('/edit_invoice', 'App\Http\Controllers\InvoiceController@updateInvoice')->name('edit_invoice');

Route::delete('/delete_invoice/{id}', 'App\Http\Controllers\InvoiceController@deleteInvoice')->name('delete_invoice');
Route::get('/send_invoice/{id}', [SendInvoice::class, 'sendInvoice'])->name('send-invoice');


Route::post('/upload', 'App\Http\Controllers\InvoiceController@upload')->name('upload');




Route::get('/generate_my_code', 'App\Http\Controllers\InvoiceController@generateMyCode');

Route::get('/dashboard', 'App\Http\Controllers\InvoiceController@getAllInvoices')->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/invoice/{id}/pdf', [PdfController::class, 'generatePdf'])->name('invoice.pdf');
Route::get('/testpdf/{id}', [PdfController::class, 'test'])->name('test-invoice');

Route::get('/test/{id}', [PdfController::class, 'testview'])->name('testview-invoice');
    Route::get('/send-email/{id}', [MailController::class, 'sendemail'])->name('send.email');

require __DIR__ . '/auth.php';
