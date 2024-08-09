<?php

namespace App\Domain\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\AdminFactory; 
class Admin extends Authenticatable
{
    use Notifiable, HasApiTokens, HasFactory;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Spécifiez la factory à utiliser pour ce modèle
    protected static function newFactory()
    {
        return AdminFactory::new();
    }
    
}
