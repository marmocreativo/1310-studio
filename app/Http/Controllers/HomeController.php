<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.public.home');
    }

    public function empresas()
    {
        return view('pages.public.empresas');
    }

    public function visitanos()
    {
        return view('pages.public.visitanos');
    }
}