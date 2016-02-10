<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Board;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;

class CityImageCommentBoard implements BoardInterface
{
    /**
     * @var City $city
     */
    protected $city;

    public function __construct()
    {

    }

    public function setCity(City $city)
    {
        $this->city = $city;
    }

    public function getTitle()
    {
        return 'Fotokommentare zur '.$this->city->getCity();
    }

    public function getDescription()
    {
        return null;
    }

    public function getThreadNumber()
    {
        return 0;
    }

    public function getPostNumber()
    {
        return 0;
    }

    public function getLastPost()
    {
        return null;
    }
}