<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
class ValidMentor extends Constraint
{
    public string $messageSelf = 'Un agent ne peut pas être son propre mentor.';
    public string $messageKia  = 'Le mentor ne peut pas être un agent KIA.';
}
