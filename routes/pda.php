<?php

use App\Http\Controllers\Sd\SdOrdController;
use Illuminate\Support\Facades\Route;

Route::get('ordenPda'     , [SdOrdController::class, 'ordenPda']);
Route::post('ordenChEstA' , [SdOrdController::class, 'ordenChEstA']);
?>