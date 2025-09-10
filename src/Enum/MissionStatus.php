<?php

namespace App\Enum;

enum MissionStatus: string
{
    case PLANNED = 'PLANNED';
    case ACTIVE = 'ACTIVE';
    case FINISHED = 'FINISHED';
}
