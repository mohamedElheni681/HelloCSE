<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfilRequest;
use App\Http\Requests\UpdateProfilRequest;
use App\Domain\Services\ProfilService;
use App\Domain\Entities\Profil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="L5 Swagger API Documentation",
 *     description="L5 Swagger OpenApi description",
 *     @OA\Contact(
 *         email="your-email@example.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class ProfilController extends Controller
{
    protected $profilService;

    public function __construct(ProfilService $profilService)
    {
        $this->profilService = $profilService;
        $this->middleware('auth:admin-api')->except('index');
    }

    /**
     * @OA\Post(
     *     path="/api/profils",
     *     tags={"Profils"},
     *     summary="Créer un nouveau profil",
     *     description="Créer un nouveau profil",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="nom", type="string", example="Doe"),
     *                 @OA\Property(property="prenom", type="string", example="John"),
     *                 @OA\Property(property="image", type="file"),
     *                 @OA\Property(property="statut", type="string", example="actif")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Profil créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profil créé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function store(StoreProfilRequest $request)
    {
        $data = $request->validated();
        $data['admin_id'] = Auth::id();
        $profil = $this->profilService->createProfil($data, $request->file('image'));
        $profil->image = url($profil->image);
        return response()->json(['message' => 'Profil créé avec succès', 'data' => $profil], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/profils",
     *     tags={"Profils"},
     *     summary="Lister les profils actifs",
     *     description="Lister les profils actifs avec pagination",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=15),
     *         description="Nombre de résultats par page"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des profils actifs paginée",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nom", type="string", example="Doe"),
     *                     @OA\Property(property="prenom", type="string", example="John"),
     *                     @OA\Property(property="image", type="string", example="image.jpg"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string", example="http://example.com?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://example.com?page=10"),
     *                 @OA\Property(property="prev", type="string", example="null"),
     *                 @OA\Property(property="next", type="string", example="http://example.com?page=2")
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="path", type="string", example="http://example.com"),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="to", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=150)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15); 
      
        if (Auth::guard('sanctum')->check()) {
            $profils = $this->profilService->getActiveProfilsWithStatus($perPage);
        } else {
            $profils = $this->profilService->getActiveProfils($perPage);
        }
        return response()->json($profils, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/profils/{profil}",
     *     tags={"Profils"},
     *     summary="Mettre à jour un profil",
     *     description="Mettre à jour un profil existant",
     *     @OA\Parameter(
     *         name="profil",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="nom", type="string", example="Doe"),
     *                 @OA\Property(property="prenom", type="string", example="John"),
     *                 @OA\Property(property="image", type="file"),
     *                 @OA\Property(property="statut", type="string", example="actif")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profil mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profil mis à jour avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function update(UpdateProfilRequest $request, Profil $profil)
    {
        $data = $request->validated();
        $image = $request->file('image');
        
        // Update profile
        $this->profilService->updateProfil($profil, $data, $image);
        
        return response()->json(['message' => 'Profil mis à jour avec succès'], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/profils/{profil}",
     *     tags={"Profils"},
     *     summary="Supprimer un profil",
     *     description="Supprimer un profil existant",
     *     @OA\Parameter(
     *         name="profil",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profil supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profil supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     security={{"sanctum":{}}}
     * )
     */
    public function destroy(Profil $profil)
    {
        $this->profilService->deleteProfil($profil);
        return response()->json(['message' => 'Profil supprimé avec succès'], 200);
    }
}
