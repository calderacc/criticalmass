<?php

namespace Criticalmass\Bundle\AppBundle\Repository;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Doctrine\ORM\EntityRepository;

class SubrideRepository extends EntityRepository
{
    public function getSubridesForRide(Ride $ride): array
    {
        $builder = $this->createQueryBuilder('sr');

        $builder
            ->select('sr')
            ->where($builder->expr()->eq('sr.ride', ':ride'))
            ->addOrderBy('sr.dateTime', 'ASC')
            ->setParameter('ride', $ride);

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

