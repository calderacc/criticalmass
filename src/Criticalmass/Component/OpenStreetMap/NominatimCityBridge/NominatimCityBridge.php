<?php

namespace Criticalmass\Component\OpenStreetMap\NominatimCityBridge;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Region;
use maxh\Nominatim\Nominatim;

class NominatimCityBridge extends AbstractNominatimCityBridge
{
    public function lookupCity(string $citySlug): ?City
    {
        $nominatim = new Nominatim(self::NOMINATIM_URL);

        $search = $nominatim->newSearch()
            ->city($citySlug)
            ->addressDetails();

        $result = $nominatim->find($search);
        $firstResult = array_shift($result);

        if ($firstResult) {
            $city = $this->createCity($firstResult);

            return $city;
        }

        return null;
    }

    protected function createCity(array $result): ?City
    {
        $region = $this->doctrine->getRepository(Region::class)->findOneByName($result['address']['state']);

        $cityName = $this->getCityNameFromResult($result);

        if (!$region || !$cityName) {
            return null;
        }

        $city = new City();
        $city
            ->setLatitude($result['lat'])
            ->setLongitude($result['lon'])
            ->setCity($cityName)
            ->setRegion($region);

        return $city;
    }

    protected function getCityNameFromResult(array $result): ?string
    {
        $propertyOrder = ['city', 'town', 'village', 'suburb'];

        foreach ($propertyOrder as $property) {
            if (array_key_exists($property, $result['address'])) {
                return $result['address'][$property];
            }
        }

        return null;
    }
}
