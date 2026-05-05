<?php

namespace App\Http\Controllers;

class EventosController extends Controller
{
    public function index()
    {
        return view('pages.public.eventos');
    }
}