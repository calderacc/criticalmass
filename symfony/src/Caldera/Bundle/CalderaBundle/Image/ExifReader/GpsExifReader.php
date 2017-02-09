<?php

namespace Caldera\Bundle\CalderaBundle\Image\ExifReader;

use Caldera\Bundle\CalderaBundle\Gps\GpsConverter;

/**
 * Class GpsExifReader
 * @package Caldera\Bundle\CalderaBundle\Image\ExifReader
 * @deprecated
 */
class GpsExifReader extends AbstractExifReader
{
    protected $latitude;
    protected $longitude;

    public function execute()
    {
        if ($this->hasGpsExifData()) {
            $gc = new GpsConverter();

            $this->latitude = $gc->convert($this->exif['GPS']['GPSLatitude']);
            $this->longitude = $gc->convert($this->exif['GPS']['GPSLongitude']);

            $this->photo->setLatitude($this->latitude);
            $this->photo->setLongitude($this->longitude);

            return true;
        }

        return false;
    }

    public function hasGpsExifData()
    {
        return (
            $this->exif &&
            array_key_exists('GPS', $this->exif) &&
            isset($this->exif['GPS']['GPSLatitude']) &&
            isset($this->exif['GPS']['GPSLongitude'])
        );
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }
}