<?php

namespace App\Enum;

enum Type: string {
    case PRESENTACION = 'Presentación';
    case CHARLA = 'Charla';
    case TALLER = 'Taller';
    case MESAREDONDA = 'Mesa Redonda';
    case EXHIBICION = 'Exhibición';
    case TORNEO = 'Torneo';
    case NETWORKING = 'Networking';
    case COMPETICION = 'Competición';
}