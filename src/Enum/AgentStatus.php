<?php

namespace App\Enum;

enum AgentStatus: string
{
    case AVAILABLE = 'AVAILABLE';
    case ON_MISSION = 'ON_MISSION';
    case RETIRED = 'RETIRED';
    case KIA = 'KIA';
}
