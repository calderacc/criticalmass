<?php

namespace Caldera\Bundle\CalderaBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\BikeShop;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataSourceController extends Controller
{
    public function loadShopsAction(Request $request): Response
    {
        $finder = $this->container->get('fos_elastica.finder.criticalmass.bikeshop');

        $topLeft = [53.0, 9.0];
        $bottomRight = [55.0, 11.0];

        $geoFilter = new \Elastica\Filter\GeoBoundingBox('pin', [$topLeft, $bottomRight]);

        $filteredQuery = new \Elastica\Query\Filtered(new \Elastica\Query\MatchAll(), $geoFilter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(15);

        $results = $finder->find($query);

        echo json_encode($query->toArray());
        echo count($results);
        /** @var BikeShop $bikeShop */
        foreach ($results as $bikeShop) {
            echo $bikeShop->getTitle();
        }

        return new Response();
    }
}
