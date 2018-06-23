<?php

namespace Criticalmass\Bundle\AppBundle\Criticalmass\Timeline\Collector;

interface TimelineCollectorInterface
{
    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime): TimelineCollectorInterface;

    public function execute(): TimelineCollectorInterface;

    public function getItems(): array;

    public function getRequiredFeatures(): array;
}
