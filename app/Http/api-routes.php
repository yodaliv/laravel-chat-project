<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers, Authorization');

Route::put('/', 'NaireController@append');
Route::delete('/{id}', 'NaireController@destroy');
Route::post('/nairesofowner', 'UserController@index');
Route::post('/result','UserController@result');
    