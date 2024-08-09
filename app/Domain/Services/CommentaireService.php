<?php

namespace App\Domain\Services;

use App\Domain\Repositories\CommentaireRepository;
use App\Domain\Entities\Commentaire;
use App\Domain\Entities\Profil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class CommentaireService
{
    protected $commentaireRepository;

    public function __construct(CommentaireRepository $commentaireRepository)
    {
        $this->commentaireRepository = $commentaireRepository;
    }

    public function createCommentaire(array $data, Profil $profil): Commentaire|JsonResponse
    {
        if ($profil->commentaires()->where('admin_id', Auth::id())->exists()) {
            \Log::info('Admin ID ' . Auth::id() . ' has already commented on profil ID ' . $profil->id);
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà posté un commentaire sur ce profil',
            ], 403);
        }

        $data['admin_id'] = Auth::id();
        $data['profil_id'] = $profil->id;

        return $this->commentaireRepository->create($data);
    }
}
