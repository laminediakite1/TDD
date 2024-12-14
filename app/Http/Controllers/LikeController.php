<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\chirp;

class LikeController extends Controller
{
    public function like(Chirp $chirp)
{
    $user = auth()->user();

    // Vérifier si l'utilisateur a déjà liké ce chirp
    if ($user->likes()->where('chirp_id', $chirp->id)->exists()) {
        return response()->json(['message' => 'Vous avez déjà liké ce chirp.'], 400);
    }

    // Liker le chirp
    $user->likes()->attach($chirp);

    return response()->json(['message' => 'Chirp liké avec succès.'], 200);
}
}
