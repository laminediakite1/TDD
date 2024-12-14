<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\chirp;
use Illuminate\Support\Carbon;


class creationChirpTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * 
     */

    //exercice 1
     public function test_un_utilisateur_peut_creer_un_chirp()
{
    // Simuler un utilisateur connecté
    $utilisateur = User::factory()->create();
    $utilisateur->markEmailAsVerified();
    $this->actingAs($utilisateur);

    // Faire une requête POST
    $reponse = $this->post('/chirps', [
        'message' => 'Mon premier chirp !',
    ]);

    // Vérifier le statut de réponse
    $reponse->assertStatus(201);

    // Vérifier que le chirp existe en base de données
    $this->assertDatabaseHas('chirps', [
        'message' => 'Mon premier chirp !',
        'user_id' => $utilisateur->id,
    ]);

}


//exercice 2
public function test_un_chirp_ne_peut_pas_avoir_un_contenu_vide()
{
 $utilisateur = User::factory()->create();
 $this->actingAs($utilisateur);
 $reponse = $this->post('/chirps', [ 'content' => ''
]);
$reponse->assertSessionHasErrors(['message']);
}

//exercice 2
public function test_un_chirp_ne_peut_pas_depasse_255_caracteres()
    {
    $utilisateur = User::factory()->create();
    $this->actingAs($utilisateur);
    $reponse = $this->post('/chirps', [
    'content' => str_repeat('a', 256)
    ]);
    $reponse->assertSessionHasErrors(['message']);
    }


    //exercice 3 (test echouer )
    public function test_les_chirps_sont_affiches_sur_la_page_d_accueil()
{
    $chirps = Chirp::factory()->count(3)->create();
    $reponse = $this->get('/');
    $reponse->assertStatus(200);
    foreach ($chirps as $chirp) {
    $reponse->assertSee($chirp->message);
    }
   }


   //exercice 4
   public function test_un_utilisateur_peut_modifier_son_chirp()
{
 $utilisateur = User::factory()->create();
 $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
 $this->actingAs($utilisateur);
 $reponse = $this->patch("/chirps/{$chirp->id}", [
 'message' => 'Chirp modifié'
 ]);
 $reponse->assertStatus(302);
 // Vérifie si le chirp existe dans la base de donnée.
 $this->assertDatabaseHas('chirps', [
 'id' => $chirp->id,
 'message' => 'Chirp modifié',
 ]);
}


//exercice 5
public function test_un_utilisateur_peut_supprimer_son_chirp()
{
 $utilisateur = User::factory()->create();
 $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
 $this->actingAs($utilisateur);
 $reponse = $this->delete("/chirps/{$chirp->id}");
 $reponse->assertStatus(302);
 $this->assertDatabaseMissing('chirps', [
    'id' => $chirp->id,
    ]);
   }


   //exercice 6
   public function test_un_utilisateur_ne_peut_pas_modifier_un_chirp_qui_ne_lui_appartient_pas()
   {
       // Créez deux utilisateurs
       $user1 = User::factory()->create();
       $user2 = User::factory()->create();

       // Créez un chirp appartenant à l'utilisateur 1
       $chirp = Chirp::factory()->create([
           'user_id' => $user1->id,
           'message' => 'Chirp de l\'utilisateur 1',
       ]);

       // Connectez-vous en tant qu'utilisateur 2
       $this->actingAs($user2);

       // Essayez de modifier le chirp de l'utilisateur 1
       $response = $this->patch("/chirps/{$chirp->id}", [
           'message' => 'Chirp modifié par utilisateur 2',
       ]);
       // Vérifiez que la réponse est une interdiction (403)
       $response->assertStatus(403);

       // Assurez-vous que le chirp n'a pas été modifié
       $this->assertDatabaseHas('chirps', [
           'id' => $chirp->id,
           'message' => 'Chirp de l\'utilisateur 1',
       ]);
   }

    
   //exercice 6
   public function test_un_utilisateur_ne_peut_pas_supprimer_un_chirp_qui_ne_lui_appartient_pas()
   {
       // Créez deux utilisateurs
       $user1 = User::factory()->create();
       $user2 = User::factory()->create();

       // Créez un chirp appartenant à l'utilisateur 1
       $chirp = Chirp::factory()->create([
           'user_id' => $user1->id,
       ]);

       // Connectez-vous en tant qu'utilisateur 2
       $this->actingAs($user2);

       // Essayez de supprimer le chirp de l'utilisateur 1
       $response = $this->delete("/chirps/{$chirp->id}");

       // Vérifiez que la réponse est une interdiction (403)
       $response->assertStatus(403);

       // Assurez-vous que le chirp n'a pas été supprimé
       $this->assertDatabaseHas('chirps', [
           'id' => $chirp->id,
       ]);
   }


   //exercice 7
   public function test_le_message_ne_peut_pas_etre_trop_long_lors_de_la_mise_a_jour()
{
    $user = User::factory()->create();
    $chirp = Chirp::factory()->create([
        'user_id' => $user->id,
        'message' => 'Ancien message',
    ]);

    $this->actingAs($user);

    $response = $this->patch(route('chirps.update', $chirp), [
        'message' => str_repeat('A', 256), // Un message de 256 caractères
    ]);

    $response->assertSessionHasErrors('message'); // Vérifie qu'une erreur est générée pour 'message'
    $this->assertDatabaseHas('chirps', [
        'id' => $chirp->id,
        'message' => 'Ancien message', // Vérifie que le message n'a pas changé
    ]);
}


//exercice 8
public function test_un_utilisateur_ne_peut_pas_avoir_plus_de_10_chirps()
{
    $user = User::factory()->create();

    // Créer 10 chirps pour l'utilisateur
    Chirp::factory()->count(10)->create(['user_id' => $user->id]);

    $this->actingAs($user);

    // Essayer de créer un 11e chirp
    $response = $this->post(route('chirps.store'), [
        'message' => 'Ce chirp ne devrait pas être créé',
    ]);

    // Vérifie qu'une erreur est renvoyée
    $response->assertStatus(400);
    $response->assertJson(['error' => 'Vous avez atteint le nombre maximum de chirps (10).']);
}





//exercice 9(test echouer)
public function test_affichage_des_chirps_recents()
{
    $user = User::factory()->create();

    // Crée un chirp dans les 7 derniers jours
    Chirp::create([
        'message' => 'Chirp récent',
        'user_id' => $user->id,
        'created_at' => Carbon::now()->subDays(5),
    ]);

    // Crée un chirp plus ancien que 7 jours
    Chirp::create([
        'message' => 'Chirp trop ancien',
        'user_id' => $user->id,
        'created_at' => Carbon::now()->subDays(10),
    ]);

    $response = $this->actingAs($user)->get(route('chirps.index'));

    // Vérifie que seul le chirp récent est affiché
    $response->assertSee('Chirp récent');
    $response->assertDontSee('Chirp trop ancien');
 
}


//exercice 10
public function test_utilisateur_peux_liker_un_chirp()
{
    // Crée un utilisateur et un chirp
    // Crée un utilisateur et un chirp
    $user = User::factory()->create();
    $chirp = Chirp::factory()->create();

    // L'utilisateur like le chirp
    $response = $this->actingAs($user)->post(route('chirps.like', $chirp));

    // Vérifie que la réponse est correcte
    $response->assertStatus(200);
    $response->assertJson(['message' => 'Chirp liké avec succès.']);

    // Vérifie que le like est bien enregistré en base
    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'chirp_id' => $chirp->id,
    ]);
}

   //exercice 10 (test echouer)
public function test_utilisateur_ne_peut_pas_liker_chips_deux_fois()
{
    // Crée un utilisateur et un chirp
    $user = User::factory()->create();
    $chirp = Chirp::factory()->create();

    // Connectez l'utilisateur
    $this->actingAs($user);

    // L'utilisateur like le chirp
    $this->postJson(route('chirps.like', $chirp))
         ->assertStatus(200);

    // L'utilisateur essaie de liker à nouveau
    $response = $this->postJson(route('chirps.like', $chirp));

    // Vérifiez que la requête est refusée
    $response->assertStatus(400);
    $response->assertJson(['message' => 'Vous avez déjà liké ce chirp.']);

    // Vérifiez qu'il n'y a qu'un seul like dans la base
    $this->assertDatabaseCount('likes', 1);
}
}