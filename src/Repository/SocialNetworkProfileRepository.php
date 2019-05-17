<?php declare(strict_types=1);

namespace App\Repository;

use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SocialNetworkProfileRepository extends EntityRepository
{
    protected function getProfileAbleQueryBuilder(bool $enabled = true): QueryBuilder
    {
        $builder = $this->createQueryBuilder('snp');

        if ($enabled) {
            $builder
                ->where($builder->expr()->eq('snp.enabled', ':enabled'))
                ->setParameter('enabled', $enabled);
        }

        return $builder;
    }

    public function findByProfileable(SocialNetworkProfileAble $profileAble): array
    {
        $reflection = new \ReflectionClass($profileAble);
        $lcEntityClassname = lcfirst($reflection->getShortName());

        $joinColumnName = sprintf('snp.%s', $lcEntityClassname);

        $queryBuilder = $this->getProfileAbleQueryBuilder();

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq($joinColumnName, ':profileAble'))
            ->setParameter('profileAble', $profileAble);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $method
     * @param array $arguments
     */
    public function __call($method, $arguments): array
    {
        $methodPrefix = 'findBy';
        $entityNamespace = 'App\\Entity';

        if (0 === strpos($method, $methodPrefix)) {
            $entityClassname = substr($method, 6);

            $fqcn = sprintf('%s\\%s', $entityNamespace, $entityClassname);
            $class = new $fqcn;

            if ($class instanceof SocialNetworkProfileAble) {
                return $this->findByProfileable($arguments[0]);
            }
        }

        return parent::__call($method, $arguments);
    }
}
