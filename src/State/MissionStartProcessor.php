<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Mission;
use App\Enum\MissionStatus;
use App\Message\MissionStarted;
use Symfony\Component\Messenger\MessageBusInterface;

final class MissionStartProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $persistProcessor, private MessageBusInterface $bus) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        \assert($data instanceof Mission);
        if ($data->getStatus() !== MissionStatus::PLANNED) {
            throw new \DomainException('Mission must be PLANNED to start');
        }
        $data->setStatus(MissionStatus::ACTIVE);
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        $this->bus->dispatch(new MissionStarted($data->getId()));
        return $result;
    }
}
