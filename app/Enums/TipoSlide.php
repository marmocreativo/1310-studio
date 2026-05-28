<?php

namespace App\Enums;

enum TipoSlide: string
{
    case Imagen = 'imagen';
    case Video  = 'video';
    case Capas  = 'capas';
}