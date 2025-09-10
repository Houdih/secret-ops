<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\{Mission, MissionResult};
use ApiPlatform\State\ProcessorInterface;
use App\Enum\{MissionStatus, MissionOutcome};
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class MissionFinishProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor
    ) {}
    
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
