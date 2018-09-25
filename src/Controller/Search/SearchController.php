<?php declare(strict_types=1);

namespace App\Controller\Search;

use App\Controller\AbstractController;
use Elastica\ResultSet;
use FOS\ElasticaBundle\Index\IndexManager;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    protected function createQuery(
        $queryPhrase//,
       // \Elastica\Filter\AbstractFilter $cityFilter,
        //\Elastica\Filter\AbstractFilter $countryFilter
    ) {
        if ($queryPhrase) {
            $simpleQueryString = new \Elastica\Query\SimpleQueryString($queryPhrase,
                ['title', 'description', 'location']);
        } else {
            $simpleQueryString = new \Elastica\Query\MatchAll();
        }

        $enabledFilter = new \Elastica\Query\Term(['isEnabled' => true]);

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($enabledFilter)
            ->addMust($simpleQueryString);

        $query = new \Elastica\Query($boolQuery);

        $query->setSize(50);
        $query->addSort('_score');

        return $query;
    }

    protected function performSearch(\Elastica\Query $query, IndexManager $manager)
    {
        $search = $manager->getIndex('criticalmass_city')->createSearch();

        //$search->addType('ride');
        //$search->addType('city');

        return $search->search($query);
    }

    protected function addAggregations(\Elastica\Query $query)
    {
        $aggregation = new \Elastica\Aggregation\Terms('city');
        $aggregation->setField('city');
        $aggregation->setSize(50);
        $query->addAggregation($aggregation);

        $aggregation = new \Elastica\Aggregation\Terms('country');
        $aggregation->setField('country');
        $aggregation->setSize(50);
        $query->addAggregation($aggregation);

        return $query;
    }

    protected function createCityFilter(array $cities = [])
    {
        $filters = [];

        foreach ($cities as $city) {
            $filters[] = new \Elastica\Filter\Term(['city' => $city]);
        }

        return new \Elastica\Filter\BoolOr($filters);
    }

    protected function createCountryFilter(array $countries = [])
    {
        $filters = [];

        foreach ($countries as $country) {
            $filters[] = new \Elastica\Filter\Term(['country' => $country]);
        }

        return new \Elastica\Filter\BoolOr($filters);
    }

    public function queryAction(Request $request, IndexManager $manager)
    {
        $queryPhrase = $request->get('query');
        $cities = $request->get('cities');
        $countries = $request->get('countries');
/*
        if ($cities) {
            $cityFilter = $this->createCityFilter($cities);
        } else {
            $cityFilter = new \Elastica\Filter\MatchAll();
        }

        if ($countries) {
            $countryFilter = $this->createCountryFilter($countries);
        } else {
            $countryFilter = new \Elastica\Filter\MatchAll();
        }
*/
        $query = $this->createQuery($queryPhrase);//, $cityFilter, $countryFilter);

        $query = $this->addAggregations($query);

        /** @var ResultSet $resultSet */
        $resultSet = $this->performSearch($query, $manager);

        $transformer = $this->get('fos_elastica.elastica_to_model_transformer.collection.criticalmass_city');

        $cityResults = $transformer->transform($resultSet->getResults());

        return $this->render('Search/result.html.twig', [
            'cityResults' => $cityResults,
            'resultSet' => $resultSet,
            'query' => $queryPhrase,
        ]);
    }
}
