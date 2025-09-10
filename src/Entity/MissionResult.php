<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Enum\MissionOutcome;
use App\Repository\MissionResultRepository;
use App\Traits\DateTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MissionResultRepository::class)]
#[ApiResource(operations: [new Get(normalizationContext: ['groups' => ['result:read']])])]
#[ORM\HasLifecycleCallbacks]
class MissionResult
{
    use DateTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['result:read', 'mission:read'])]
    private ?int $id = null;

    #[ORM\Column(enumType: MissionOutcome::class)]
    #[Groups(['result:read', 'mission:read'])]
    private MissionOutcome $outcome;

    #[ORM\Column(type: 'text')]
    #[Groups(['result:read', 'mission:read'])]
    private string $summary;

    #[ORM\OneToOne(inversedBy: 'result')] 
    #[ORM\JoinColumn(nullable: false, unique: true, onDelete: 'CASCADE')]
    private Mission $mission;

    public function __construct(Mission $mission, MissionOutcome $outcome, string $summary)
    {
        $this->mission = $mission;
        $this->outcome = $outcome;
        $this->summary = $summary;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOutcome(): ?MissionOutcome
    {
        return $this->outcome;
    }

    public function setOutcome(MissionOutcome $outcome): static
    {
        $this->outcome = $outcome;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): static
    {
        $this->summary = $summary;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMission(): Mission
    {
        return $this->mission;
    }

    public function setMission(Mission $mission): static
    {
        $this->mission = $mission;

        return $this;
    }
}
