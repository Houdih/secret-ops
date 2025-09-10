<?php

namespace App\Entity;

use App\Traits\DateTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MessageRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\{ApiResource, Get, GetCollection, Post};

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ApiResource(operations: [
    new GetCollection(normalizationContext: ['groups' => ['message:list']]),
    new Get(normalizationContext: ['groups' => ['message:read']]),
    new Post(denormalizationContext: ['groups' => ['message:write']], normalizationContext: ['groups' => ['message:read']]),
])]
#[ORM\HasLifecycleCallbacks]
class Message
{
    use DateTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['message:list', 'message:read', 'agent:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 140)]
    #[Groups(['message:list', 'message:read', 'message:write'])]
    private string $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['message:read', 'message:write'])]
    private string $body;

    #[ORM\Column(length: 80, nullable: true)]
    #[Groups(['message:read', 'message:write'])]
    private ?string $reason = null; // "MISSION_STARTED", "AGENT_KIA", ...

    #[ORM\ManyToOne] #[ORM\JoinColumn(nullable: false)]
    #[Groups(['message:read', 'message:write'])]
    private Agent $recipient;

    #[ORM\ManyToOne] // nullable: messages système lors des events
    #[Groups(['message:read'])]
    private ?Agent $author = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getRecipient(): ?Agent
    {
        return $this->recipient;
    }

    public function setRecipient(?Agent $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getAuthor(): ?Agent
    {
        return $this->author;
    }

    public function setAuthor(?Agent $author): static
    {
        $this->author = $author;

        return $this;
    }
}
