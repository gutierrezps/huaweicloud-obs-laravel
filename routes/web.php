<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ObsController;

Route::get('/', [ObsController::class, 'list']);
Route::post('/upload', [ObsController::class, 'upload']);
Route::delete('/delete', [ObsController::class, 'delete']);
