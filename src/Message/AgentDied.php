<?php

namespace App\Message;

final class AgentDied
{
    public function __construct(public readonly int $agentId) {}
}
