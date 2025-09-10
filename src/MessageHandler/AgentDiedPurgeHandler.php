<?php

namespace App\MessageHandler;

use App\Message\AgentDied;
use App\Entity\Message as AgentMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AgentDiedPurgeHandler
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(AgentDied $event): void
    {
        // Suppression de tous les messages ÉMIS par l’agent mort
        $qb = $this->em->createQueryBuilder()
            ->delete(AgentMessage::class, 'm')
            ->where('m.author = :a')->setParameter('a', $event->agentId);

        $qb->getQuery()->execute();
    }
}
