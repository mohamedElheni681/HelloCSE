<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ProfilController;


Route::post('admin/register', [AdminController::class, 'register']);
Route::post('admin/login', [AdminController::class, 'login']);

Route::middleware('auth:admin-api')->group(function () {
    Route::post('/profils', [ProfilController::class, 'store']);
    Route::put('/profils/{profil}', [ProfilController::class, 'update']);
    Route::delete('/profils/{profil}', [ProfilController::class, 'destroy']);
});

Route::get('/profils', [ProfilController::class, 'index']);