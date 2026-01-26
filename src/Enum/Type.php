<?php

namespace App\Enum;

enum Type: string {
    case PRESENTACION = 'Presentacion';
    case CHARLA = 'Charla';
    case TALLER = 'Taller';
    case MESAREDONDA = 'Mesa Redonda';
    case EXHIBICION = 'Exhibicion';
    case TORNEO = 'Torneo';
    case NETWORKING = 'Networking';
}