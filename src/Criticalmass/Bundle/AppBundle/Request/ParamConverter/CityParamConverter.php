<?php

namespace Criticalmass\Bundle\AppBundle\Request\ParamConverter;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class CityParamConverter extends AbstractParamConverter
{
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $city = $this->findCityById($request);

        if (!$city) {
            $city = $this->findCityBySlug($request);;
        }

        if ($city) {
            $request->attributes->set($configuration->getName(), $city);
        } else {
            $this->notFound($configuration);
        }
    }

    protected function findCityById(Request $request): ?City
    {
        $cityId = $request->get('cityId');

        if ($cityId) {
            $city = $this->registry->getRepository(City::class)->find($cityId);

            return $city;
        }

        return null;
    }
}
