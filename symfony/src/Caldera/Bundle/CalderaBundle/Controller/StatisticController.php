<?php

namespace Caldera\Bundle\CalderaBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\FacebookCityProperties;
use Caldera\Bundle\CalderaBundle\Entity\Region;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;

class StatisticController extends AbstractController
{
    public function citystatisticAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $rides = $this->getRideRepository()->findRidesForCity($city);

        $this->getMetadata()->setDescription('Critical-Mass-Statistiken aus ' . $city->getCity() . ': Teilnehmer, Fahrtdauer, Fahrtlänge, Touren');

        return $this->render(
            'CalderaBundle:Statistic:citystatistic.html.twig',
            [
                'city' => $city,
                'rides' => $rides
            ]
        );
    }

    public function ridestatisticAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $frp = $this->getFacebookRidePropertiesRepository()->findByRide($ride);

        return $this->render(
            'CalderaBundle:Statistic:ridestatistic.html.twig',
            [
                'ride' => $ride,
                'frp' => $frp
            ]
        );
    }

    public function facebookstatisticAction(Request $request)
    {
        $cityPropertiesList = $this->getFacebookCityPropertiesRepository()->findForStatistic();

        $filteredProperties = [];

        $cityList = [];
        $dayList = [];

        /**
         * @var FacebookCityProperties $facebookCityProperties
         */
        foreach ($cityPropertiesList as $facebookCityProperties) {
            $citySlug = $facebookCityProperties->getCity()->getSlug();
            $day = $facebookCityProperties->getCreatedAt()->format('Y-m-d');

            $filteredProperties[$citySlug][$day] = $facebookCityProperties;
            $dayList[$day] = $day;
            $cityList[$citySlug] = $facebookCityProperties->getCity();
        }

        return $this->render(
            'CalderaBundle:Statistic:facebookstatistic.html.twig',
            [
                'cities' => $cityList,
                'filteredProperties' => $filteredProperties,
                'days' => $dayList
            ]
        );
    }

    public function overviewAction(Request $request)
    {
        /**
         * @var Region $region
         */
        $region = $this->getRegionRepository()->find(3);

        $endDateTime = new \DateTime();
        $twoYearInterval = new \DateInterval('P2Y');

        $startDateTime = new \DateTime();
        $startDateTime->sub($twoYearInterval);

        $rides = $this->getRideRepository()->findRidesInRegionInInterval($region, $startDateTime, $endDateTime);

        $citiesWithoutEstimates = $this->findCitiesWithoutParticipationEstimates($rides);
        $rides = $this->filterRideList($rides, $citiesWithoutEstimates);

        $cities = [];

        $rideMonths = [];

        /**
         * @var Ride $ride
         */
        foreach ($rides as $ride) {
            $cities[$ride->getCity()->getSlug()] = $ride->getCity();

            $rideMonths[$ride->getDateTime()->format('Y-m')] = $ride->getDateTime()->format('Y-m');
        }

        rsort($rideMonths);

        $this->getMetadata()->setDescription('Critical-Mass-Statistiken: Teilnehmer, Fahrtdauer, Fahrtlänge, Touren');

        return $this->render(
            'CalderaBundle:Statistic:overview.html.twig',
            [
                'cities' => $cities,
                'rides' => $rides,
                'rideMonths' => $rideMonths
            ]
        );
    }

    protected function findCitiesWithoutParticipationEstimates(array $rides)
    {
        $cityList = [];

        /**
         * @var Ride $ride
         */
        foreach ($rides as $ride) {
            if (!$ride->getEstimatedParticipants()) {
                $citySlug = $ride->getCity()->getSlug();

                if (array_key_exists($citySlug, $cityList)) {
                    ++$cityList[$citySlug];
                } else {
                    $cityList[$citySlug] = 1;
                }
            }
        }

        return $cityList;
    }

    protected function filterRideList(array $rides, array $cities)
    {
        $resultList = [];

        /**
         * @var Ride $ride
         */
        foreach ($rides as $ride) {
            $citySlug = $ride->getCity()->getSlug();

            if (!array_key_exists($citySlug, $cities) || $cities[$citySlug] < 18) {
                $resultList[] = $ride;
            }
        }

        return $resultList;
    }
}
