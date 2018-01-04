<?php

namespace CCriticalmass\Component\Image\PhotoGps;

use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Bundle\AppBundle\Gps\GpxReader\TrackReader;
use CCriticalmass\Component\Image\ExifReader\DateTimeExifReader;
use CCriticalmass\Component\Image\ExifReader\GpsExifReader;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @deprecated
 */
class PhotoGps
{
    /**
     * @var Track $track
     */
    protected $track;

    /**
     * @var Photo $photo
     */
    protected $photo;

    /**
     * @var array $exifData
     */
    protected $exifData;

    /**
     * @var UploaderHelper $uploaderHelper
     */
    protected $uploaderHelper;

    /**
     * @var string $rootDirectory
     */
    protected $rootDirectory;

    /**
     * @var TrackReader $trackReader
     */
    protected $trackReader;

    /**
     * @var GpsExifReader $gpsExifReader
     */
    protected $gpsExifReader;

    /**
     * @var DateTimeExifReader $dateTimeExifReader
     */
    protected $dateTimeExifReader;

    /** @var \DateTimeZone */
    protected $dateTimeZone;

    public function __construct(UploaderHelper $uploaderHelper, $rootDirectory, GpsExifReader $gpsExifReader, DateTimeExifReader $dateTimeExifReader, TrackReader $trackReader)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->rootDirectory = $rootDirectory . '/../web';
        $this->gpsExifReader = $gpsExifReader;
        $this->dateTimeExifReader = $dateTimeExifReader;
        $this->trackReader = $trackReader;
    }

    public function setDateTimeZone(\DateTimeZone $dateTimeZone = null)
    {
        $this->dateTimeZone = $dateTimeZone;
        $this->trackReader->setDateTimeZone($dateTimeZone);

        return $this;
    }

    public function setPhoto(Photo $photo)
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPhoto(Photo $photo)
    {
        return $this->photo;
    }

    public function setTrack(Track $track)
    {
        $this->track = $track;

        return $this;
    }

    public function execute()
    {
        $this->gpsExifReader->setPhoto($this->photo);

        if ($this->gpsExifReader->hasGpsExifData()) {
            $this->gpsExifReader->execute();

            $this->photo = $this->gpsExifReader->getPhoto();
        } elseif ($this->track) {
            $this->approximateCoordinates();
        }

        return $this;
    }

    protected function readExifData()
    {
        $filename = $this->uploaderHelper->asset($this->photo, 'imageFile');

        $this->exifData = exif_read_data($filename, 0, true);
    }

    protected function xmlToDateTime($xml)
    {
        return new \DateTime(str_replace("T", " ", str_replace("Z", "", $xml)));
    }

    public function approximateCoordinates()
    {
        $this->trackReader->loadTrack($this->track);

        $dateTime = $this->dateTimeExifReader
            ->setPhoto($this->photo)
            ->execute()
            ->getDateTime();

        $result = $this->trackReader->findCoordNearDateTime($dateTime);

        $this->photo->setLatitude($result['latitude']);
        $this->photo->setLongitude($result['longitude']);
    }
}
