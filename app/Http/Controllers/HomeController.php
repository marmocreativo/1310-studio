<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.public.home');
    }

    public function estudio()
    {
        return view('pages.public.estudio');
    }

    public function empresas()
    {
        return view('pages.public.empresas');
    }
}