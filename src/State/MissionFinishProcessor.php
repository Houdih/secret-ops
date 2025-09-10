<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\{Mission, MissionResult};
use App\Enum\{MissionStatus, MissionOutcome};
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class MissionFinishProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $persistProcessor) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        \assert($data instanceof Mission);
        if ($data->getStatus() !== MissionStatus::ACTIVE) {
            throw new BadRequestHttpException('Mission must be ACTIVE to finish');
        }

        $payload = $context['request']->toArray(); // outcome + summary
        $outcome = MissionOutcome::from(($payload['outcome'] ?? ''));
        $summary = (string)($payload['summary'] ?? '');

        $data->setStatus(MissionStatus::FINISHED);
        $result = new MissionResult($data, $outcome, $summary);
        $data->setResult($result);

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
