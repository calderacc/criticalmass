<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\EventSubscriber;

use Criticalmass\Bundle\AppBundle\Event\Participation\ParticipationCreatedEvent;
use Criticalmass\Bundle\AppBundle\Event\Participation\ParticipationDeletedEvent;
use Criticalmass\Bundle\AppBundle\Event\Participation\ParticipationUpdatedEvent;
use Criticalmass\Component\Participation\Calculator\RideParticipationCalculatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ParticipationEventSubscriber implements EventSubscriberInterface
{
    /** @var RideParticipationCalculatorInterface $rideParticipationCalculator */
    protected $rideParticipationCalculator;

    public function __construct(RideParticipationCalculatorInterface $rideParticipationCalculator)
    {
        $this->rideParticipationCalculator = $rideParticipationCalculator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ParticipationCreatedEvent::NAME => 'onParticipationCreated',
            ParticipationUpdatedEvent::NAME => 'onParticipationUpdated',
            ParticipationDeletedEvent::NAME => 'onParticipationDeleted',
        ];
    }

    public function onParticipationCreated(ParticipationCreatedEvent $participationCreatedEvent): void
    {
        $this->rideParticipationCalculator
            ->setRide($participationCreatedEvent->getParticipation()->getRide())
            ->calculate();
    }

    public function onParticipationUpdated(ParticipationUpdatedEvent $participationUpdatedEvent): void
    {
        $this->rideParticipationCalculator
            ->setRide($participationUpdatedEvent->getParticipation()->getRide())
            ->calculate();
    }

    public function onParticipationDeleted(ParticipationDeletedEvent $participationDeletedEvent): void
    {
        $this->rideParticipationCalculator
            ->setRide($participationDeletedEvent->getParticipation()->getRide())
            ->calculate();
    }
}
