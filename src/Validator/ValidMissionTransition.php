<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
class ValidMissionTransition extends Constraint
{
    public string $message = 'Transition de statut invalide (PLANNED -> ACTIVE -> FINISHED).';
}
