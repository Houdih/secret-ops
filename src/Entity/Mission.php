<?php

namespace App\Entity;

use App\Repository\MissionRepository;
use ApiPlatform\Metadata\{ApiResource, Get, GetCollection, Post, Patch};
use App\Enum\{MissionStatus, MissionDanger};
use App\State\{MissionStartProcessor, MissionFinishProcessor};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
#[ORM\Table(
    name: 'mission',
    indexes: [
        new ORM\Index(name: 'idx_mission_country', columns: ['country_id']),
        new ORM\Index(name: 'idx_mission_status', columns: ['status']),
        new ORM\Index(name: 'idx_mission_danger', columns: ['danger']),
        new ORM\Index(name: 'idx_mission_start', columns: ['start_date']),
    ]
)]
#[ApiResource(operations: [
    new GetCollection(normalizationContext: ['groups' => ['mission:list']]),
    new Get(normalizationContext: ['groups' => ['mission:read']]),
    new Post(denormalizationContext: ['groups' => ['mission:write']], normalizationContext: ['groups' => ['mission:read']]),
    new Patch(denormalizationContext: ['groups' => ['mission:write']], normalizationContext: ['groups' => ['mission:read']]),
    // opérations métier
    new Post(name: 'start', uriTemplate: '/missions/{id}/start', processor: MissionStartProcessor::class),
    new Post(name: 'finish', uriTemplate: '/missions/{id}/finish', processor: MissionFinishProcessor::class),
])]
class Mission
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['mission:list', 'mission:read', 'agent:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 160)]
    #[Groups(['mission:list', 'mission:read', 'mission:write'])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['mission:read', 'mission:write'])]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['mission:read', 'mission:write'])]
    private ?string $objectives = null;

    #[ORM\Column(enumType: MissionStatus::class)]
    #[Groups(['mission:list', 'mission:read', 'mission:write'])]
    #[AppAssert\ValidMissionTransition]
    private MissionStatus $status = MissionStatus::PLANNED;

    #[ORM\Column(enumType: MissionDanger::class)]
    #[Groups(['mission:list', 'mission:read', 'mission:write'])]
    private MissionDanger $danger;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['mission:read', 'mission:write'])]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['mission:read', 'mission:write'])]
    #[Assert\Expression(
        "this.getStartDate() === null or this.getEndDate() === null or this.getEndDate() >= this.getStartDate()",
        message: "endDate doit être postérieure ou égale à startDate."
    )]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    #[Groups(['mission:list', 'mission:read', 'mission:write'])]
    private Country $country;

    #[ORM\ManyToMany(targetEntity: Agent::class)]
    #[ORM\JoinTable(name: 'mission_agent')]
    #[ORM\JoinColumn(name: 'mission_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'agent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Groups(['mission:read', 'mission:write'])]
    private Collection $agents;

    #[ORM\OneToOne(mappedBy: 'mission', targetEntity: MissionResult::class, cascade: ['persist', 'remove'])]
    #[Groups(['mission:read'])]
    private ?MissionResult $result = null;

    public function __construct()
    {
        $this->agents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getObjectives(): ?string
    {
        return $this->objectives;
    }

    public function setObjectives(?string $objectives): static
    {
        $this->objectives = $objectives;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, Agent>
     */
    public function getAgents(): Collection
    {
        return $this->agents;
    }

    public function addAgent(Agent $agent): static
    {
        if (!$this->agents->contains($agent)) {
            $this->agents->add($agent);
        }

        return $this;
    }

    public function removeAgent(Agent $agent): static
    {
        $this->agents->removeElement($agent);

        return $this;
    }

    public function getResult(): ?MissionResult
    {
        return $this->result;
    }

    public function setResult(?MissionResult $result): static
    {
        // On maintient la bidirectionnalité sans tenter de rendre la FK nulle (non-nullable)
        $this->result = $result;
        if ($result && $result->getMission() !== $this) {
            $result->setMission($this);
        }
        return $this;
    }

    public function getStatus(): MissionStatus
    {
        return $this->status;
    }

    public function setStatus(MissionStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDanger(): MissionDanger
    {
        return $this->danger;
    }

    public function setDanger(MissionDanger $danger): static
    {
        $this->danger = $danger;

        return $this;
    }
}
