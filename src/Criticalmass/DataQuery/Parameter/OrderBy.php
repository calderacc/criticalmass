<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use Elastica\Query;

class OrderBy implements ParameterInterface
{
    public function addToElasticQuery(Query $query): Query
    {
        return $query;
    }
}