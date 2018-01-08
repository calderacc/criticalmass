<?php

namespace Criticalmass\Bundle\AppBundle\Command\Facebook;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\Facebook\FacebookEventRideApi;
use Facebook\Facebook;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RidePropertiesCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var ObjectManager $manager */
    protected $manager;

    /** @var Facebook $facebook */
    protected $facebook;

    protected function configure()
    {
        $this
            ->setName('criticalmass:facebook:rideproperties')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();

        /** @var FacebookEventRideApi $fera */
        $fera = $this->getContainer()->get('caldera.criticalmass.facebookapi.eventride');

        $rides = $this->doctrine->getRepository('AppBundle:Ride')->findRidesWithFacebookInInterval();

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $output->writeln('Looking up ' . $ride->getFancyTitle());

            $eventId = $this->getEventId($ride);

            if ($eventId) {
                $output->writeln('Event ID is: ' . $eventId);

                $properties = $fera->getEventPropertiesForRide($ride);

                if ($properties) {
                    $this->manager->persist($properties);

                    $output->writeln('Saved properties');
                    $output->writeln('');
                }
            }
        }

        $this->manager->flush();
    }

    protected function getEventId(Ride $ride): ?string
    {
        $facebook = $ride->getFacebook();

        if (strpos($facebook, 'https://www.facebook.com/') == 0) {
            $facebook = rtrim($facebook, "/");

            $parts = explode('/', $facebook);

            $eventId = array_pop($parts);

            return $eventId;
        }

        return null;
    }

}