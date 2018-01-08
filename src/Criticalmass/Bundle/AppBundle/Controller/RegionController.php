<?php

namespace Criticalmass\Bundle\AppBundle\Controller;

use Abraham\TwitterOAuth\Request;

class RegionController extends AbstractController
{
    public function indexAction($slug1 = null, $slug2 = null, $slug3 = null)
    {
        $region = null;

        if ($slug1 && $slug2 && $slug3) {
            $region = $this->getRegionRepository()->findOneBySlug($slug3);
        } elseif ($slug1 && $slug2) {
            $region = $this->getRegionRepository()->findOneBySlug($slug2);
        } elseif ($slug1) {
            $region = $this->getRegionRepository()->findOneBySlug($slug1);
        } else {
            $region = $this->getRegionRepository()->find(1);
        }

        $cities = $this->getCityRepository()->findCitiesOfRegion($region);
        $allCities = $this->getCityRepository()->findChildrenCitiesOfRegion($region);
        $regions = $this->getRegionRepository()->findByParentRegion($region);

        $cityCounter = [];

        // do not name it $region as $region is already in use
        foreach ($regions as $region2) {
            $cityCounter[$region2->getId()] = $this->getCityRepository()->countChildrenCitiesOfRegion($region2);
        }

        return $this->render(
            'AppBundle:Region:index.html.twig',
            [
                'region' => $region,
                'regions' => $regions,
                'cities' => $cities,
                'cityCounter' => $cityCounter,
                'allCities' => $allCities
            ]
        );
    }
}
