<?php

namespace App\MessageHandler;

use App\Entity\Agent;
use App\Message\MissionStarted;
use App\Repository\MissionRepository;
use App\Entity\Message as AgentMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MissionStartedHandler
{
    public function __construct(private MissionRepository $missions, private EntityManagerInterface $em) {}

    public function __invoke(MissionStarted $event): void
    {
        $mission = $this->missions->find($event->missionId);
        if (!$mission) return;

        $country = $mission->getCountry();
        $participants = $mission->getAgents()->toArray();

        // Tous les agents du pays sauf participants
        $agentsInCountry = $this->em->getRepository(Agent::class)
            ->createQueryBuilder('a')
            ->where('a.currentCountry = :c')->setParameter('c', $country)
            ->getQuery()->getResult();

        foreach ($agentsInCountry as $agent) {
            if (in_array($agent, $participants, true)) continue;
            $msg = new AgentMessage();
            $msg->setTitle('Mission started');
            $msg->setBody(sprintf('Mission "%s" a démarré dans %s.', $mission->getName(), $country->getName()));
            $msg->setReason('MISSION_STARTED');
            $msg->setRecipient($agent);
            $this->em->persist($msg);
        }
        $this->em->flush();
    }
}
