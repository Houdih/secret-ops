<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\Mission;
use App\Enum\MissionDanger;
use App\Enum\MissionStatus;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Mission>
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    public function maxActiveDangerByCountry(Country $c): ?MissionDanger
    {
        $qb = $this->createQueryBuilder('m')
            ->select('MAX(m.danger) as maxDanger')
            ->where('m.country = :c')->setParameter('c', $c)
            ->andWhere('m.status = :active')->setParameter('active', MissionStatus::ACTIVE)
            ->getQuery()->getSingleScalarResult();

        return $qb ? \App\Enum\MissionDanger::from($qb) : null;
    }
}
