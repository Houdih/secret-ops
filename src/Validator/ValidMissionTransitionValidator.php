<?php

namespace App\Validator;

use App\Entity\Mission;
use App\Enum\MissionStatus;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidMissionTransitionValidator extends ConstraintValidator
{
    /** @param Mission $mission */
    public function validate($mission, Constraint $constraint)
    {
        if (!$mission instanceof Mission) return;
        // Ici, on valide surtout lors des mutations manuelles (PATCH/PUT)
        // Les processors start/finish sécurisent déjà les transitions.
        $status = $mission->getStatus();
        $result = $mission->getResult();

        if ($status === MissionStatus::FINISHED && !$result) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
