<?php

namespace App\Validator;

use App\Entity\Agent;
use App\Enum\AgentStatus;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidMentorValidator extends ConstraintValidator
{
    /** @param Agent $value */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof Agent) return;
        $mentor = $value->getMentor();
        if (!$mentor) return;

        if ($mentor->getId() && $value->getId() && $mentor->getId() === $value->getId()) {
            $this->context->buildViolation($constraint->messageSelf)->addViolation();
        }
        if ($mentor->getStatus() === AgentStatus::KIA) {
            $this->context->buildViolation($constraint->messageKia)->addViolation();
        }
        // (anti-cycle simple à ajouter si mentor->mentor == value)
    }
}
