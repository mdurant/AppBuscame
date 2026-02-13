<?php

namespace App\Enums;

enum PropertyType: string
{
    case Casa = 'casa';
    case Departamento = 'departamento';
    case Habitacion = 'habitacion';
    case Estudio = 'estudio';
    case Otro = 'otro';
}
