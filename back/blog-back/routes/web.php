<?php

use Illuminate\Support\Facades\DB;
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
    return view('welcome');
});


Route::get('/db-test', function () {
    try {
        $connectionStatus = DB::connection()->getPdo();
        return 'Connecté avec succès à la base de données: ' . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return 'Erreur lors de la connexion à la base de données : ' . $e->getMessage();
    }
});
