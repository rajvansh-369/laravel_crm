<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('contact-list');
});

Route::post('/contact', [ContactController::class, 'createContact']);
Route::get('/contact-list', [ContactController::class, 'contactList']);
// Route to search for contacts to merge into
Route::get('/contacts/search-for-merge', [ContactController::class, 'searchForMerge'])->name('contacts.searchForMerge');

// Route to perform the actual merge operation
Route::post('/contacts/merge', [ContactController::class, 'merge'])->name('contacts.merge');

// Route to delete a specific contact
Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');


Route::post('/contacts/{contact}/update', [ContactController::class, 'update'])->name('contacts.update');
