<?php

namespace Criticalmass\Component\Image\PhotoGps;

use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Component\Gps\GpxReader\TrackReader;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

abstract class AbstractPhotoGps implements PhotoGpsInterface
{
    /** @var Track $track */
    protected $track;

    /** @var Photo $photo */
    protected $photo;

    /** @var array $exifData */
    protected $exifData;

    /** @var UploaderHelper $uploaderHelper */
    protected $uploaderHelper;

    /** @var string $uploadDestinationPhoto */
    protected $uploadDestinationPhoto;

    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var \DateTimeZone */
    protected $dateTimeZone;

    public function __construct(
        UploaderHelper $uploaderHelper,
        TrackReader $trackReader,
        string $uploadDestinationPhoto
    ) {
        $this->uploaderHelper = $uploaderHelper;
        $this->uploadDestinationPhoto = $uploadDestinationPhoto;
        $this->trackReader = $trackReader;
    }

    public function setDateTimeZone(\DateTimeZone $dateTimeZone = null): PhotoGpsInterface
    {
        $this->dateTimeZone = $dateTimeZone;
        $this->trackReader->setDateTimeZone($dateTimeZone);

        return $this;
    }

    public function setPhoto(Photo $photo): PhotoGpsInterface
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPhoto(): Photo
    {
        return $this->photo;
    }

    public function setTrack(Track $track = null): PhotoGpsInterface
    {
        $this->track = $track;

        return $this;
    }
}
