<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Ride;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class ParticipationRepository extends EntityRepository
{
    public function findParticipationForUserAndRide(User $user, Ride $ride)
    {
        $builder = $this->createQueryBuilder('participation');

        $builder->select('participation');
        $builder->where($builder->expr()->eq('participation.user', $user->getId()));
        $builder->andWhere($builder->expr()->eq('participation.ride', $ride->getId()));
        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function countParticipationsForRide(Ride $ride, $status)
    {
        $builder = $this->createQueryBuilder('participation');

        $builder->select('COUNT(participation)');
        $builder->where($builder->expr()->eq('participation.ride', $ride->getId()));

        $builder->andWhere($builder->expr()->eq('participation.goingYes', ($status == 'yes' ? 1 : 0)));
        $builder->andWhere($builder->expr()->eq('participation.goingMaybe', ($status == 'maybe' ? 1 : 0)));
        $builder->andWhere($builder->expr()->eq('participation.goingNo', ($status == 'no' ? 1 : 0)));

        $builder->setMaxResults(1);

        $query = $builder->getQuery();

        return $query->getSingleScalarResult();
    }

    public function findByUser(User $user): array
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->join('p.ride', 'r')
            ->where($builder->expr()->eq('p.user', ':user'))
            ->setParameter('user', $user)
            ->orderBy('r.dateTime', 'DESC')
        ;

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

