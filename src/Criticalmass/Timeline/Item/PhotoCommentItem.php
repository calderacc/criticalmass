<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Timeline\Item;

use AppBundle\Entity\Photo;

class PhotoCommentItem extends AbstractItem
{
    /** @var Photo $photo */
    public $photo;

    /** @var string $rideTitle */
    public $rideTitle;

    /** @var string $text */
    public $text;

    public function getPhoto(): Photo
    {
        return $this->photo;
    }

    public function setPhoto(Photo $photo): PhotoCommentItem
    {
        $this->photo = $photo;

        return $this;
    }

    public function getRideTitle(): string
    {
        return $this->rideTitle;
    }

    public function setRideTitle(string $rideTitle): PhotoCommentItem
    {
        $this->rideTitle = $rideTitle;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): PhotoCommentItem
    {
        $this->text = $text;

        return $this;
    }
}
