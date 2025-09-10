<?php 
namespace App\Enum;

enum MessageReason: string
{
    case MISSION_STARTED = 'MISSION_STARTED';
    case AGENT_KIA       = 'AGENT_KIA';
    case SYSTEM          = 'SYSTEM';
    case MANUAL          = 'MANUAL';
}
