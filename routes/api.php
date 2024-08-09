<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\CommentaireController;


Route::post('admin/register', [AdminController::class, 'register']);
Route::post('admin/login', [AdminController::class, 'login']);

Route::middleware('auth:admin-api')->group(function () {
    Route::post('/profils', [ProfilController::class, 'store']);
    Route::put('/profils/{profil}', [ProfilController::class, 'update']);
    Route::delete('/profils/{profil}', [ProfilController::class, 'destroy']);
    Route::post('/profils/{profil}/commentaires', [CommentaireController::class, 'store']);
});

Route::get('/profils', [ProfilController::class, 'index']);