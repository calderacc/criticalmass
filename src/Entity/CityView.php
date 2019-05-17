<?php declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\ViewInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="city_view")
 * @ORM\Entity
 */
class CityView implements ViewInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $dateTime;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): ViewInterface
    {
        $this->user = $user;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): ViewInterface
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city = null): CityView
    {
        $this->city = $city;

        return $this;
    }
}
