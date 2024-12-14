<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use illuminate\Http\Response;
use App\Models\chirp;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Carbon;



class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function index(): View{

        $chirps = Chirp::with('user')
        ->where('created_at', '>=', Carbon::now()->subDays(7)) // Filtrer les chirps créés dans les 7 derniers jours
        ->latest()
        ->get();


        return view('chirps.index',
    ['chirps' => Chirp::with('user')->latest()->get(),
   
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {

         // Vérifie si l'utilisateur a déjà 10 "chirps"
    if ($request->user()->chirps()->count() >= 10) {
        return response()->json(['error' => 'Vous avez atteint le nombre maximum de chirps (10).'], 400);
    }
        $validated = $request->validate([
            'message'=> 'required|string|max:255',
        ]);

        $request->user()->chirps()->create($validated);

       //return redirect(route('chirps.index'));
       return response()->json(['message' => 'Chirp created!'], 201);

       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function show(chirp $chirp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function edit(chirp $chirp): view
    {

        Gate::authorize('update', $chirp);

        return view('chirps.edit',[
            'chirp' => $chirp,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, chirp $chirp): RedirectResponse
    {
        
        Gate::authorize('update', $chirp);

        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $chirp->update($validated);

        return redirect(route('chirps.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function destroy(chirp $chirp):RedirectResponse
    {
        Gate::authorize('delete', $chirp);

        $chirp->delete();

        return redirect(route('chirps.index'));
    }
}
