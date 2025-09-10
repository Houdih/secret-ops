<?php

namespace App\MessageHandler;

use App\Entity\Agent;
use App\Enum\AgentStatus;
use App\Message\AgentDied;
use App\Entity\Message as AgentMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AgentDiedNotifyHandler
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(AgentDied $event): void
    {
        $agent = $this->em->getRepository(Agent::class)->find($event->agentId);
        if (!$agent) return;

        // Broadcast à tous les agents vivants
        $aliveAgents = $this->em->getRepository(Agent::class)->createQueryBuilder('a')
            ->where('a.status != :kia')->setParameter('kia', AgentStatus::KIA)
            ->getQuery()->getResult();

        foreach ($aliveAgents as $recipient) {
            $msg = new AgentMessage();
            $msg->setTitle('Agent KIA');
            $msg->setBody(sprintf('Agent "%s" est KIA.', $agent->getCodename()));
            $msg->setReason('AGENT_KIA');
            $msg->setRecipient($recipient);
            $msg->setAuthor(null);
            $this->em->persist($msg);
        }
        $this->em->flush();
    }
}
