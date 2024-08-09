<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\ProfilFactory; 

class Profil extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom', 'prenom', 'image', 'statut', 'admin_id',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    protected static function newFactory()
    {
        return ProfilFactory::new();
    }
}
