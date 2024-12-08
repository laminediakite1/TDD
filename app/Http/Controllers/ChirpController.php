<?php

namespace App\Http\Controllers;
use illuminate\Http\Response;
use App\Models\chirp;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function index(): View{

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
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message'=> 'required|string|max:255',
        ]);

        $request->user()->chirps()->create($validated);

        return redirect(route('chirps.index'));
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
    public function edit(chirp $chirp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, chirp $chirp)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\chirp  $chirp
     * @return \Illuminate\Http\Response
     */
    public function destroy(chirp $chirp)
    {
        //
    }
}
