<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\{ApiResource, Get, GetCollection, Post};
use App\Enum\MissionDanger;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Index(name: 'idx_country_name', columns: ['name'])]
#[ApiResource(operations: [
    new GetCollection(normalizationContext: ['groups' => ['country:list']]),
    new Get(normalizationContext: ['groups' => ['country:read']]),
    new Post(denormalizationContext: ['groups' => ['country:write']]),
])]
class Country
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['country:list', 'country:read', 'mission:read', 'agent:read'])]

    private ?int $id = null;

    #[ORM\Column(length: 128, unique: true)]
    #[Groups(['country:list', 'country:read', 'country:write', 'mission:read', 'agent:read'])]
    private string $name;

    // Danger non persisté : servi via un State Provider (max danger des missions actives)
    #[Groups(['country:read'])]
    private ?MissionDanger $danger = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setDanger(?MissionDanger $d): void
    {
        $this->danger = $d;
    }
    public function getDanger(): ?MissionDanger
    {
        return $this->danger;
    }

}
