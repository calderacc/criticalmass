<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Region;
use Doctrine\ORM\EntityRepository;

class CityRepository extends EntityRepository
{
    /**
     * @deprecated
     */
    public function findCitiesWithFacebook(): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->isNotNull('c.facebook'))
            ->orderBy('c.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesOfRegion(Region $region): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder->select('c');

        $builder
            ->where($builder->expr()->eq('c.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('c.region', ':region'))
            ->setParameter('region', $region)
            ->andWhere($builder->expr()->neq('c.latitude', ':notLatitude'))
            ->setParameter('notLatitude', 0)
            ->andWhere($builder->expr()->neq('c.longitude', ':notLongitude'))
            ->setParameter('notLongitude', 0);

        $builder->orderBy('c.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function countChildrenCitiesOfRegion(Region $region): int
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('COUNT(c)')
            ->leftJoin('c.region', 'r1')
            ->leftJoin('r1.parent', 'r2')
            ->leftJoin('r2.parent', 'r3')
            ->where($builder->expr()->eq('c.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere(
                $builder->expr()->orX(
                   $builder->expr()->eq('r1', ':region1'),
                    $builder->expr()->eq('r2', ':region2'),
                    $builder->expr()->eq('r3', ':region3')
                )
            )
            ->setParameter('region1', $region)
            ->setParameter('region2', $region)
            ->setParameter('region3', $region);

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function findChildrenCitiesOfRegion(Region $region): array
    {
        $builder = $this->createQueryBuilder('city');

        $builder
            ->select('c')
            ->leftJoin('c.region', 'r1')
            ->leftJoin('r1.parent', 'r2')
            ->leftJoin('r2.parent', 'r3')
            ->where($builder->expr()->eq('c.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere(
                $builder->expr()->orX(
                    $builder->expr()->eq('r1', ':region1'),
                    $builder->expr()->eq('r2', ':region2'),
                    $builder->expr()->eq('r3', ':region3')
                )
            )
            ->setParameter('region1', $region)
            ->setParameter('region2', $region)
            ->setParameter('region3', $region);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findEnabledCities(): array
    {
        return $this->findCities();
    }

    public function findCities(): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->eq('c.enabled', ':enabled'))
            ->orderBy('c.city', 'ASC')
            ->setParameter('enabled', true);

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesWithBoard(): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->eq('c.enabled', ':enabled'))
            ->setParameter('enabled', true)
            ->andWhere($builder->expr()->eq('c.enableBoard', ':enableBoard'))
            ->setParameter('enableBoard', true)
            ->orderBy('c.city', 'ASC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesByAverageParticipants($limit = 10)
    {
        $query = $this->getEntityManager()->createQuery("SELECT IDENTITY(r.city) AS city, c.city AS cityName, SUM(r.estimatedParticipants) / COUNT(c.id) AS averageParticipants FROM App:Ride r JOIN r.city c GROUP BY r.city ORDER BY averageParticipants DESC")->setMaxResults($limit);

        return $query->getResult();
    }

    public function findForTimelineCityEditCollector(
        \DateTime $startDateTime = null,
        \DateTime $endDateTime = null,
        int $limit = null
    ): array {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->isNotNull('c.updatedAt'))
            ->addOrderBy('c.updatedAt', 'DESC');

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('c.updatedAt', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('c.updatedAt', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder
                ->setMaxResults($limit);
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findForTimelineCityCreatedCollector(
        \DateTime $startDateTime = null,
        \DateTime $endDateTime = null,
        int $limit = null
    ): array {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->where($builder->expr()->isNull('c.updatedAt'))
            ->addOrderBy('c.createdAt', 'DESC');

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('c.createdAt', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('c.createdAt', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        if ($limit) {
            $builder
                ->setMaxResults($limit);
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findCitiesBySlugList(array $slugList): array
    {
        $builder = $this->createQueryBuilder('c');

        $builder
            ->select('c')
            ->join('c.slugs', 's')
            ->where($builder->expr()->in('s.slug', ':slugList'))
            ->orderBy('c.city', 'ASC')
            ->setParameter('slugList', $slugList);

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

