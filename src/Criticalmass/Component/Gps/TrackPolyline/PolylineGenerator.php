<?php declare(strict_types=1);

namespace Criticalmass\Component\Gps\TrackPolyline;

class PolylineGenerator extends AbstractPolylineGenerator
{
    public function execute(): PolylineGeneratorInterface
    {
        $list = $this->trackReader->slicePublicCoords();

        $polyline = \Polyline::Encode($list);

        $this->polyline = $polyline;

        return $this;
    }
} 