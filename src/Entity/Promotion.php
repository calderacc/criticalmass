<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\Router\Annotation as Routing;
use App\EntityInterface\AutoParamConverterAble;
use App\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="promotion")
 * @ORM\Entity(repositoryClass="App\Repository\PromotionRepository")
 * @Routing\DefaultRoute(name="caldera_criticalmass_promotion_show")
 */
class Promotion implements AutoParamConverterAble, RouteableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Routing\RouteParameter(name="promotionSlug")
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $query;

    /**
     * @ORM\Column(type="boolean")
     */
    private $showMap;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $mapCenterLatitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $mapCenterLongitude;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mapZoomLevel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function hasMap(): ?bool
    {
        return $this->showMap;
    }

    public function getShowMap(): ?bool
    {
        return $this->showMap;
    }

    public function setShowMap(bool $showMap): self
    {
        $this->showMap = $showMap;

        return $this;
    }

    public function getMapCenterLatitude(): ?float
    {
        return $this->mapCenterLatitude;
    }

    public function setMapCenterLatitude(?float $mapCenterLatitude): self
    {
        $this->mapCenterLatitude = $mapCenterLatitude;

        return $this;
    }

    public function getMapCenterLongitude(): ?float
    {
        return $this->mapCenterLongitude;
    }

    public function setMapCenterLongitude(?float $mapCenterLongitude): self
    {
        $this->mapCenterLongitude = $mapCenterLongitude;

        return $this;
    }

    public function getMapZoomLevel(): ?int
    {
        return $this->mapZoomLevel;
    }

    public function setMapZoomLevel(?int $mapZoomLevel): self
    {
        $this->mapZoomLevel = $mapZoomLevel;

        return $this;
    }
}
