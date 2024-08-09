<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentaireRequest;
use App\Domain\Services\CommentaireService;
use App\Domain\Entities\Profil;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class CommentaireController extends Controller
{
    protected $commentaireService;

    public function __construct(CommentaireService $commentaireService)
    {
        $this->commentaireService = $commentaireService;
        // Utilisation du middleware pour protéger les endpoints avec authentification admin
        $this->middleware('auth:admin-api');
    }

    /**
     * @OA\Post(
     *     path="/api/profils/{profil}/commentaires",
     *     tags={"Commentaires"},
     *     summary="Ajouter un commentaire sur un profil",
     *     description="Ajouter un commentaire sur un profil. Un administrateur ne peut poster qu’un commentaire sur un profil.",
     *     @OA\Parameter(
     *         name="profil",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="contenu", type="string", example="Ceci est un commentaire.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Commentaire ajouté avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Commentaire ajouté avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Vous avez déjà posté un commentaire sur ce profil"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function store(StoreCommentaireRequest $request, Profil $profil)
    {
        // Validation des données du commentaire
        $data = $request->validated();
        // Appel au service pour créer le commentaire
        $commentaire = $this->commentaireService->createCommentaire($data, $profil);

        if ($commentaire instanceof JsonResponse) {
            return $commentaire;
        }
        // Réponse JSON avec le commentaire ajouté
        return response()->json(['message' => 'Commentaire ajouté avec succès', 'data' => $commentaire], 201);
    }
}
