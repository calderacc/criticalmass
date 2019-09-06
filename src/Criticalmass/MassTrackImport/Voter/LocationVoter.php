<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Criticalmass\Geo\DistanceCalculator\DistanceCalculator;
use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Entity\Ride;
use Caldera\GeoBasic\Coord\Coord;

class LocationVoter implements VoterInterface
{
    public function vote(Ride $ride, StravaActivityModel $model): float
    {
        $rideCoord = new Coord($ride->getLatitude(), $ride->getLongitude());
        $activityCoord = $model->getStartCoord();

        $distance = DistanceCalculator::calculateDistance($rideCoord, $activityCoord);

        if ($distance < 1) {
            return 1.0;
        }

        if ($distance < 5) {
            return 0.9;
        }

        if ($distance < 25) {
            return 0.8;
        }

        if ($distance < 50) {
            return 0.5;
        }

        return -1.0;
    }
}
