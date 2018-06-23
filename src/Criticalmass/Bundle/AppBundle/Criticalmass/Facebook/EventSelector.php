<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Criticalmass\Facebook;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
use Criticalmass\Bundle\AppBundle\Criticalmass\Facebook\Bridge\RideBridge;
use Criticalmass\Bundle\AppBundle\Criticalmass\SocialNetwork\Network\FacebookEvent;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Facebook\GraphNodes\GraphEvent;

class EventSelector
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var RideBridge $rideBridge */
    protected $rideBridge;

    /** @var array $assignedRides */
    protected $assignedRides = [];

    public function __construct(Doctrine $doctrine, RideBridge $rideBridge)
    {
        $this->doctrine = $doctrine;
        $this->rideBridge = $rideBridge;
    }

    public function autoselect(): EventSelector
    {
        $rides = $this->doctrine->getRepository(Ride::class)->findRecentRides(2018, 4);

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            if (!$this->hasFacebookEvent($ride)) {
                /** @var GraphEvent $event */
                $event = $this->rideBridge->getEventForRide($ride);

                if ($event) {
                    $eventId = $event->getId();

                    $link = sprintf('https://www.facebook.com/events/%s', $eventId);

                    $ride->setFacebook($link);

                    $this->assignedRides[] = $ride;
                }
            }
        }

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function getAssignedRides(): array
    {
        return $this->assignedRides;
    }

    protected function hasFacebookEvent(Ride $ride): bool
    {
        /** @var SocialNetworkProfile $socialNetworkProfile */
        foreach ($ride->getSocialNetworkProfiles() as $socialNetworkProfile) {
            if ($socialNetworkProfile->getNetwork() === 'facebook_event') {
                return true;
            }
        }

        return false;
    }

    protected function createEventProfile(Ride $ride, GraphEvent $event): SocialNetworkProfile
    {
        $eventId = $event->getId();
        $link = sprintf('https://www.facebook.com/events/%s', $eventId);

        $profile = new SocialNetworkProfile();

        $profile
            ->setRide($ride)
            ->setNetwork('facebook_event')
            ->setIdentifier($link);

        return $profile;
    }
}
