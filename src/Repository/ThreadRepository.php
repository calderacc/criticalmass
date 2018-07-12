<?php

namespace App\Repository;

use App\Entity\Board;
use App\Entity\City;
use Doctrine\ORM\EntityRepository;

/**
 * @package App\Repository
 * @author maltehuebner
 * @since 2016-02-13
 */
class ThreadRepository extends EntityRepository
{
    public function findThreadsForBoard(Board $board)
    {
        $builder = $this->createQueryBuilder('thread');

        $builder->select('thread');

        $builder->leftJoin('thread.lastPost', 'lastPost');

        $builder->where($builder->expr()->eq('thread.board', $board->getId()));
        $builder->andWhere($builder->expr()->eq('thread.enabled', 1));

        $builder->orderBy('lastPost.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findThreadsForCity(City $city)
    {
        $builder = $this->createQueryBuilder('thread');

        $builder->select('thread');

        $builder->leftJoin('thread.lastPost', 'lastPost');

        $builder->where($builder->expr()->eq('thread.city', $city->getId()));
        $builder->andWhere($builder->expr()->eq('thread.enabled', 1));

        $builder->orderBy('lastPost.dateTime', 'DESC');

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function findThreadBySlug($slug)
    {
        $builder = $this->createQueryBuilder('thread');

        $builder->select('thread');
        $builder->where($builder->expr()->eq('thread.enabled', 1));
        $builder->andWhere($builder->expr()->eq('thread.slug', '\'' . $slug . '\''));

        $query = $builder->getQuery();

        return $query->getSingleResult();
    }

    public function findForTimelineThreadCollector(
        \DateTime $startDateTime = null,
        \DateTime $endDateTime = null,
        $limit = null
    ) {
        $builder = $this->createQueryBuilder('thread');

        $builder->select('thread');

        $builder->join('thread.firstPost', 'firstPost');

        $builder->where($builder->expr()->eq('thread.enabled', 1));

        if ($startDateTime) {
            $builder->andWhere($builder->expr()->gte('firstPost.dateTime',
                '\'' . $startDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($endDateTime) {
            $builder->andWhere($builder->expr()->lte('firstPost.dateTime',
                '\'' . $endDateTime->format('Y-m-d H:i:s') . '\''));
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('firstPost.dateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }
}

