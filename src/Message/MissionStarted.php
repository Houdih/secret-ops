<?php

namespace App\Message;

final class MissionStarted
{
    public function __construct(public readonly int $missionId) {}
}
