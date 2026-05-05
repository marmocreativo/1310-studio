<?php

namespace App\Http\Controllers;

use App\Models\Taller;

class TalleresController extends Controller
{
    public function index()
    {
        $proximos = Taller::activos()->proximos()->get();
        $pasados  = Taller::activos()->pasados()->paginate(9);

        return view('pages.public.talleres.index', compact('proximos', 'pasados'));
    }

    public function show(Taller $taller)
    {
        if (! $taller->estado) {
            abort(404);
        }

        return view('pages.public.talleres.show', compact('taller'));
    }
}