<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\CommentaireFactory; 

class Commentaire extends Model
{
    use HasFactory;
    protected $fillable = [
        'contenu', 'admin_id', 'profil_id',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function profil()
    {
        return $this->belongsTo(Profil::class);
    }

    protected static function newFactory()
    {
        return CommentaireFactory::new();
    }

    
}
