<?php

use Illuminate\Support\Facades\Route;

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
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::group(['middleware' => 'auth'], function () {
//Diagrama
Route::get('/diagramas', [App\Http\Controllers\DiagramController::class, 'index']);

Route::get('/diagramas/create', [App\Http\Controllers\DiagramController::class, 'create']);
Route::get('/diagramas/{diagram}/edit', [App\Http\Controllers\DiagramController::class, 'edit']);
Route::post('/diagramas', [App\Http\Controllers\DiagramController::class, 'sendData']);

Route::put('/diagramas/{diagram}', [App\Http\Controllers\DiagramController::class, 'update']);

Route::delete('/diagramas/{diagram}', [App\Http\Controllers\DiagramController::class, 'destroy']);

//Inivtaciones
Route::get('/diagramas/{diagram}/invitaciones', [App\Http\Controllers\DiagramController::class, 'invitar']);
Route::post('/diagramas/invitaciones', [App\Http\Controllers\DiagramController::class, 'sendInvitation']);

//Colaboraciones
Route::get('/colaboraciones', [App\Http\Controllers\InvitedController::class, 'index']);

//Pizarra
Route::get('/diagramas/{diagram}/pizarra', [App\Http\Controllers\PizarraController::class, 'index']);
Route::post('/diagramas/pizarra', [App\Http\Controllers\PizarraController::class, 'savePizarra']);
Route::get('/diagramas/{diagram}/descargar', [App\Http\Controllers\DiagramController::class, 'descargar']);

});