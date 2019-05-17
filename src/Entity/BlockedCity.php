<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlockedCityRepository")
 * @ORM\Table(name="city_blocked")
 */
class BlockedCity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\City", fetch="LAZY")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $blockStart;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $blockEnd;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $twitter;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $photosLink;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $rideListLink;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setBlockStart(\DateTime $blockStart): BlockedCity
    {
        $this->blockStart = $blockStart;

        return $this;
    }

    public function getBlockStart(): ?\DateTime
    {
        return $this->blockStart;
    }

    public function setBlockEnd(\DateTime $blockEnd): BlockedCity
    {
        $this->blockEnd = $blockEnd;

        return $this;
    }

    public function getBlockEnd(): ?\DateTime
    {
        return $this->blockEnd;
    }

    public function setDescription(string $description): BlockedCity
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setUrl(string $url): BlockedCity
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setFacebook(string $facebook): BlockedCity
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setTwitter(string $twitter): BlockedCity
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setPhotosLink(bool $photosLink): BlockedCity
    {
        $this->photosLink = $photosLink;

        return $this;
    }

    public function getPhotosLink(): bool
    {
        return $this->photosLink;
    }

    public function setRideListLink(bool $rideListLink): BlockedCity
    {
        $this->rideListLink = $rideListLink;

        return $this;
    }

    public function getRideListLink(): bool
    {
        return $this->rideListLink;
    }

    public function setCity(City $city = null): BlockedCity
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }
}
