<?php declare(strict_types=1);

namespace Criticalmass\Component\Gps\TrackTimeshift;

use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Component\Gps\GpxExporter\GpxExporter;
use Criticalmass\Component\Gps\GpxReader\TrackReader;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @deprecated
 */
abstract class AbstractTrackTimeshift implements TrackTimeshiftInterface
{
    /** @var RegistryInterface $doctrine */
    protected $doctrine;

    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var GpxExporter $gpxExporter */
    protected $gpxExporter;

    /** @var Track $track */
    protected $track;

    /** @var array $positionArray */
    protected $positionArray;

    public function __construct(RegistryInterface $doctrine, TrackReader $trackReader, GpxExporter $gpxExporter)
    {
        $this->doctrine = $doctrine;
        $this->trackReader = $trackReader;
        $this->gpxExporter = $gpxExporter;
    }

    public function loadTrack(Track $track): TrackTimeshiftInterface
    {
        $this->track = $track;

        $this->trackReader->loadTrack($this->track);

        $this->positionArray = $this->trackReader->getAsPositionArray();

        return $this;
    }

    public function saveTrack(): TrackTimeshiftInterface
    {
        $this->gpxExporter->setPositionArray($this->positionArray);

        $this->gpxExporter->execute();

        $gpxContent = $this->gpxExporter->getGpxContent();

        $filename = $this->track->getTrackFilename();

        $fp = fopen('../web/tracks/' . $filename, 'w');
        fwrite($fp, $gpxContent);
        fclose($fp);

        return $this;
    }
}
