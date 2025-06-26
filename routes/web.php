<?php

use Illuminate\Support\Facades\Route;

use App\Models\EmailTemplate;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/preview-email-template/{id}', function ($id) {
    $template = EmailTemplate::findOrFail($id);
    return view('emails.preview', compact('template'));
})->name('preview.email');

