<?php

namespace App\Domain\Services;

use App\Domain\Repositories\ProfilRepository;
use Illuminate\Http\UploadedFile;
use App\Domain\Entities\Profil;
use Illuminate\Support\Facades\Storage;

class ProfilService
{
    protected $profilRepository;

    public function __construct(ProfilRepository $profilRepository)
    {
        $this->profilRepository = $profilRepository;
    }

    public function createProfil(array $data, UploadedFile $image): Profil
    {
        $profil = $this->profilRepository->create($data);

        $imageName = $image->getClientOriginalName();

        $imagePath = "images/profils/{$profil->id}";

        // Stocker l'image dans le répertoire public en utilisant le disque 'public'
        $storedPath = Storage::disk('public')->putFileAs($imagePath, $image, $imageName);

        // Mettre à jour le champ 'image' avec le chemin complet de l'image
        $profil->update(['image' => $storedPath]);

        return $profil;
    }


    public function updateProfil(Profil $profil, array $data, ?UploadedFile $image = null): bool
    {
        if ($image) {
            // Supprimer l'ancienne image si elle existe
            if ($profil->image && Storage::disk('public')->exists($profil->image)) {
                Storage::disk('public')->delete($profil->image);
            }

            $imageName = $image->getClientOriginalName();
            $imagePath = "images/profils/{$profil->id}";

            $storedPath = Storage::disk('public')->putFileAs($imagePath, $image, $imageName);
    
            $data['image'] = $storedPath;
        }
        
        // Mettez à jour uniquement les champs fournis dans $data
        foreach ($data as $key => $value) {
            $profil->$key = $value;
        }
        return $profil->save();
    }

    public function deleteProfil(Profil $profil): bool
    {
        return $this->profilRepository->delete($profil);
    }

    public function getActiveProfils($perPage = 15)
    {
        return $this->profilRepository->findActive($perPage);
    }
}
