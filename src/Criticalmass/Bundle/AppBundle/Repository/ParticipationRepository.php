<?php

namespace Criticalmass\Bundle\AppBundle\Repository;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

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

    public function countByUser(User $user): int
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('COUNT(p)')
            ->where($builder->expr()->eq('p.user', ':user'))
            ->setParameter('user', $user)
            ->andWhere($builder->expr()->eq('p.goingYes', true))
        ;

        $query = $builder->getQuery();

        return $query->getSingleScalarResult();
    }

    public function findByUser(User $user, bool $yes = false, bool $maybe = false, bool $no = false): array
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->join('p.ride', 'r')
            ->where($builder->expr()->eq('p.user', ':user'))
            ->setParameter('user', $user)
            ->orderBy('r.dateTime', 'DESC')
        ;

        if ($yes) {
            $builder->andWhere($builder->expr()->eq('p.goingYes', true));
        }

        if ($maybe) {
            $builder->andWhere($builder->expr()->eq('p.goingMaybe', true));
        }

        if ($no) {
            $builder->andWhere($builder->expr()->eq('p.goingNo', true));
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

