<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\MapLocation;
use App\Http\Livewire\graf;
use App\Http\Livewire\GrafLocation;
use App\Http\Livewire\Simpul;
use App\Http\Livewire\Simpull;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return view('admin');
});
Route::get('/admin', function () {
    return view('admin');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/map', MapLocation::class);
Route::get('/graf', GrafLocation::class);
Route::get('/simpul', Simpull::class);
