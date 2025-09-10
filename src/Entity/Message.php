<?php

namespace App\Entity;

use App\Traits\DateTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MessageRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\MessageReason;
use ApiPlatform\Metadata\{ApiResource, Get, GetCollection, Post};

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(
    name: 'message',
    indexes: [
        new ORM\Index(name: 'idx_message_recipient', columns: ['recipient_id']),
        new ORM\Index(name: 'idx_message_author', columns: ['author_id']),
        new ORM\Index(name: 'idx_message_created_at', columns: ['created_at']),
    ]
)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(operations: [
    new GetCollection(normalizationContext: ['groups' => ['message:list']]),
    new Get(normalizationContext: ['groups' => ['message:read']]),
    new Post(denormalizationContext: ['groups' => ['message:write']], normalizationContext: ['groups' => ['message:read']]),
])]
class Message
{
    use DateTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['message:list', 'message:read', 'agent:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 140)]
    #[Assert\NotBlank]
    #[Groups(['message:list', 'message:read', 'message:write'])]
    private string $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['message:read', 'message:write'])]
    private string $body;

    #[ORM\Column(enumType: MessageReason::class, nullable: true)]
    #[Groups(['message:read', 'message:write'])]
    private ?MessageReason $reason = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['message:read', 'message:write'])]
    private Agent $recipient;

    // author nullable : permet des messages “système”
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
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
