<?php

namespace App\Entity;

use App\Repository\AgentRepository;
use ApiPlatform\Metadata\{ApiResource, Get, GetCollection, Post, Patch};
use App\Enum\AgentStatus;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: AgentRepository::class)]
#[UniqueEntity(fields: ['codename'], message: 'Ce nom de code est déjà pris.')]
#[UniqueEntity(fields: ['user'], message: 'Un utilisateur ne peut être lié qu’à un seul agent.')]
#[ORM\Index(name: 'idx_agent_status', columns: ['status'])]
#[ApiResource(operations: [
    new GetCollection(normalizationContext: ['groups' => ['agent:list']]),
    new Get(normalizationContext: ['groups' => ['agent:read']]),
    new Post(denormalizationContext: ['groups' => ['agent:write']], normalizationContext: ['groups' => ['agent:read']]),
    new Patch(denormalizationContext: ['groups' => ['agent:write']], normalizationContext: ['groups' => ['agent:read']]),
])]
class Agent
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['agent:list', 'agent:read', 'mission:read', 'message:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 64, unique: true)]
    #[Groups(['agent:list', 'agent:read', 'agent:write', 'mission:read', 'message:read'])]
    #[Assert\NotBlank]
    private string $codename;

    #[ORM\Column(length: 32, enumType: AgentStatus::class)]
    #[Groups(['agent:read', 'agent:write'])]
    private AgentStatus $status = AgentStatus::AVAILABLE;

    #[ORM\Column(type: 'integer')]
    #[Assert\PositiveOrZero]
    #[Groups(['agent:read', 'agent:write'])]
    private int $yearsOfExperience = 0;

    #[ORM\Column(type: 'date_immutable')]
    #[Assert\NotNull]
    #[Groups(['agent:read', 'agent:write'])]
    private \DateTimeImmutable $enrolmentDate;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[Groups(['agent:read', 'agent:write'])]
    #[AppAssert\ValidMentor] // mentor ≠ self, pas KIA, pas de cycle
    private ?self $mentor = null;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[Groups(['agent:read', 'agent:write', 'mission:read'])]
    private ?Country $currentCountry = null;

    #[ORM\OneToOne(inversedBy: 'agent', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE', unique: true)]
    private User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodename(): ?string
    {
        return $this->codename;
    }

    public function setCodename(string $codename): static
    {
        $this->codename = $codename;

        return $this;
    }

    public function getStatus(): ?AgentStatus
    {
        return $this->status;
    }

    public function setStatus(AgentStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getYearsOfExperience(): ?int
    {
        return $this->yearsOfExperience;
    }

    public function setYearsOfExperience(int $yearsOfExperience): static
    {
        $this->yearsOfExperience = $yearsOfExperience;

        return $this;
    }

    public function getEnrolmentDate(): ?\DateTimeImmutable
    {
        return $this->enrolmentDate;
    }

    public function setEnrolmentDate(\DateTimeImmutable $enrolmentDate): static
    {
        $this->enrolmentDate = $enrolmentDate;

        return $this;
    }

    public function getMentor(): ?self
    {
        return $this->mentor;
    }

    public function setMentor(?self $mentor): static
    {
        $this->mentor = $mentor;

        return $this;
    }

    public function getCurrentCountry(): ?Country
    {
        return $this->currentCountry;
    }

    public function setCurrentCountry(?Country $currentCountry): static
    {
        $this->currentCountry = $currentCountry;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
