<?php

namespace AppBundle\Entity;

use AppBundle\EntityInterface\AuditableInterface;
use AppBundle\EntityInterface\ElasticSearchPinInterface;
use AppBundle\EntityInterface\ParticipateableInterface;
use AppBundle\EntityInterface\PhotoInterface;
use AppBundle\EntityInterface\RouteableInterface;
use AppBundle\EntityInterface\ViewableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraint as CriticalAssert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="ride")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RideRepository")
 * @JMS\ExclusionPolicy("all")
 * @CriticalAssert\SingleRideForDay
 * @Vich\Uploadable
 */
class Ride implements ParticipateableInterface, ViewableInterface, ElasticSearchPinInterface, PhotoInterface, RouteableInterface, AuditableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="rides", fetch="LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="CityCycle", inversedBy="rides", fetch="LAZY")
     * @ORM\JoinColumn(name="cycle_id", referencedColumnName="id")
     */
    protected $cycle;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="rides", fetch="LAZY")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected $city;

    /**
     * @ORM\OneToMany(targetEntity="Track", mappedBy="ride", fetch="LAZY")
     */
    protected $tracks;

    /**
     * @ORM\OneToMany(targetEntity="Subride", mappedBy="ride", fetch="LAZY")
     */
    protected $subrides;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $socialDescription;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @JMS\Type("DateTime<'U'>")
     */
    protected $dateTime;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $hasTime = false;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $hasLocation = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected $location;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected $longitude;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @JMS\Expose
     */
    protected $estimatedParticipants;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     */
    protected $estimatedDistance;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     */
    protected $estimatedDuration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     * @JMS\Groups({"ride-list"})
     * @JMS\Expose
     */
    protected $url;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="ride", fetch="LAZY")
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="ride", fetch="LAZY")
     */
    protected $photos;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $participationsNumberYes = 0;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $participationsNumberMaybe = 0;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    protected $participationsNumberNo = 0;

    /**
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="ride", fetch="LAZY")
     */
    protected $participations;

    /**
     * @ORM\OneToMany(targetEntity="RideEstimate", mappedBy="ride", fetch="LAZY")
     */
    protected $estimates;

    /**
     * @ORM\Column(type="integer")
     */
    protected $views = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Photo", inversedBy="featuredRides", fetch="LAZY")
     * @ORM\JoinColumn(name="featured_photo", referencedColumnName="id")
     */
    protected $featuredPhoto;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $restrictedPhotoAccess = false;

    /**
     * @ORM\OneToMany(targetEntity="Weather", mappedBy="ride", fetch="LAZY")
     * @ORM\OrderBy({"creationDateTime" = "DESC"})
     */
    protected $weathers;

    /**
     * @Vich\UploadableField(mapping="ride_photo", fileNameProperty="imageName")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imageName;


    public function __construct()
    {
        $this->dateTime = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->visibleSince = new \DateTime();
        $this->visibleUntil = new \DateTime();
        $this->expectedStartDateTime = new \DateTime();

        $this->weathers = new ArrayCollection();
        $this->estimates = new ArrayCollection();
        $this->tracks = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->subrides = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): Ride
    {
        $this->user = $user;

        return $this;
    }

    public function getCycle(): ?CityCycle
    {
        return $this->cycle;
    }

    public function setCycle(CityCycle $cityCycle = null): Ride
    {
        $this->cycle = $cityCycle;

        return $this;
    }

    public function setDateTime(\DateTime $dateTime): Ride
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    /**
     * @deprecated
     */
    public function getSimpleDate(): string
    {
        return $this->dateTime->format('Y-m-d');
    }
    
    public function setHasTime(bool $hasTime): Ride
    {
        $this->hasTime = $hasTime;

        return $this;
    }

    public function getHasTime(): bool
    {
        return $this->hasTime;
    }

    public function setHasLocation(bool $hasLocation): Ride
    {
        $this->hasLocation = $hasLocation;

        return $this;
    }

    public function getHasLocation(): bool
    {
        return $this->hasLocation;
    }

    public function setLocation(string $location = null): Ride
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setCity(City $city = null): Ride
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setLatitude(float $latitude = null): Ride
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude = null): Ride
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setTitle(string $title = null): Ride
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(string $description): Ride
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setSocialDescription(string $socialDescription): Ride
    {
        $this->socialDescription = $socialDescription;

        return $this;
    }

    public function getSocialDescription(): ?string
    {
        return $this->socialDescription;
    }

    /** @deprecated */
    public function isEqual(Ride $ride): bool
    {
        return $ride->getId() == $this->getId();
    }

    /** @deprecated */
    public function equals(Ride $ride): bool
    {
        return $this->isEqual($ride);
    }

    /** @deprecated */
    public function isSameRide(Ride $ride): bool
    {
        return $ride->getCity()->getId() == $this->getCity()->getId() && $ride->getFormattedDate() == $this->getFormattedDate();
    }

    public function __toString(): string
    {
        if ($this->city) {
            return $this->city->getTitle() . " - " . $this->getDateTime()->format('Y-m-d');
        } else {
            return $this->getDateTime()->format('Y-m-d');
        }
    }

    public function setEstimatedParticipants(int $estimatedParticipants): Ride
    {
        $this->estimatedParticipants = $estimatedParticipants;

        return $this;
    }

    public function getEstimatedParticipants(): ?int
    {
        return $this->estimatedParticipants;
    }

    public function setFacebook(string $facebook = null): Ride
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setTwitter(string $twitter = null): Ride
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setUrl(string $url = null): Ride
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /** @deprecated */
    public function getFormattedDate(): string
    {
        return $this->dateTime->format('Y-m-d');
    }

    /** @deprecated */
    public function getDate(): \DateTime
    {
        return $this->dateTime;
    }

    /** @deprecated */
    public function getTime(): \DateTime
    {
        return $this->dateTime;
    }

    /** @deprecated */
    public function setDate(\DateTime $date): Ride
    {
        $this->dateTime = new \DateTime($date->format('Y-m-d') . ' ' . $this->dateTime->format('H:i:s'), $date->getTimezone());

        return $this;
    }

    /** @deprecated */
    public function setTime(\DateTime $time): Ride
    {
        $this->dateTime = new \DateTime($this->dateTime->format('Y-m-d') . ' ' . $time->format('H:i:s'), $time->getTimezone());

        return $this;
    }

    public function setEstimatedDistance(float $estimatedDistance): Ride
    {
        $this->estimatedDistance = $estimatedDistance;

        return $this;
    }

    public function getEstimatedDistance(): ?float
    {
        return $this->estimatedDistance;
    }

    public function setEstimatedDuration(float $estimatedDuration): Ride
    {
        $this->estimatedDuration = $estimatedDuration;

        return $this;
    }

    public function getEstimatedDuration(): ?float
    {
        return $this->estimatedDuration;
    }

    public function addTrack(Track $track): Ride
    {
        $this->tracks->add($track);

        return $this;
    }

    public function removeTrack(Track $track): Ride
    {
        $this->tracks->removeElement($track);

        return $this;
    }

    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("title")
     * @JMS\Type("string")
     * @deprecated
     */
    public function getFancyTitle(): string
    {
        if (!$this->title) {
            return $this->city->getTitle() . ' ' . $this->dateTime->format('d.m.Y');
        }

        return $this->getTitle();
    }

    public function addPost(Post $post): Ride
    {
        $this->posts->add($post);

        return $this;
    }

    public function removePost(Post $posts): Ride
    {
        $this->posts->removeElement($posts);

        return $this;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPhoto(Photo $photo): Ride
    {
        $this->photos->add($photo);

        return $this;
    }

    public function removePhoto(Photo $photos): Ride
    {
        $this->photos->removeElement($photos);

        return $this;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addSubride(Subride $subride): Ride
    {
        $this->subrides->add($subride);

        return $this;
    }

    public function removeSubride(Subride $subrides): Ride
    {
        $this->subrides->removeElement($subrides);

        return $this;
    }

    public function getSubrides(): Collection
    {
        return $this->subrides;
    }

    public function getDurationInterval(): \DateInterval
    {
        $totalMinutes = $this->estimatedDuration * 60.0;

        $hours = floor($totalMinutes / 60.0);
        $minutes = $totalMinutes % 60.0;

        return new \DateInterval('PT' . $hours . 'H' . $minutes . 'M');
    }

    public function getAverageVelocity(): int
    {
        if (!$this->getEstimatedDuration() || !$this->getEstimatedDistance()) {
            return 0;
        }

        return $this->getEstimatedDistance() / $this->getEstimatedDuration();
    }

    public function getPin(): string
    {
        if (!$this->latitude || !$this->longitude) {
            return '0,0';
        }
        
        return $this->latitude . ',' . $this->longitude;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): Ride
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): Ride
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setParticipationsNumberYes(int $participationsNumberYes): ParticipateableInterface
    {
        $this->participationsNumberYes = $participationsNumberYes;

        return $this;
    }

    public function getParticipationsNumberYes(): int
    {
        return $this->participationsNumberYes;
    }

    public function setParticipationsNumberMaybe(int $participationsNumberMaybe): ParticipateableInterface
    {
        $this->participationsNumberMaybe = $participationsNumberMaybe;

        return $this;
    }

    public function getParticipationsNumberMaybe(): int
    {
        return $this->participationsNumberMaybe;
    }

    public function setParticipationsNumberNo(int $participationsNumberNo): ParticipateableInterface
    {
        $this->participationsNumberNo = $participationsNumberNo;

        return $this;
    }

    public function getParticipationsNumberNo(): int
    {
        return $this->participationsNumberNo;
    }

    public function setViews(int $views): ViewableInterface
    {
        $this->views = $views;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function incViews(): ViewableInterface
    {
        ++$this->views;

        return $this;
    }

    public function getCountry(): ?Region
    {
        if ($this->city) {
            return $this->city->getCountry();
        }

        return null;
    }

    public function getIsEnabled(): ?bool
    {
        if ($this->city) {
            return $this->city->isEnabled();
        }

        return null;
    }

    public function setFeaturedPhoto(Photo $featuredPhoto = null): Ride
    {
        $this->featuredPhoto = $featuredPhoto;

        return $this;
    }

    public function getFeaturedPhoto(): ?Photo
    {
        return $this->featuredPhoto;
    }

    /** @deprecated */
    public function getRestrictedPhotoAccess(): bool
    {
        return $this->restrictedPhotoAccess;
    }

    /** @deprecated */
    public function setRestrictedPhotoAccess(bool $restrictedPhotoAccess): Ride
    {
        $this->restrictedPhotoAccess = $restrictedPhotoAccess;

        return $this;
    }

    public function addParticipation(Participation $participation): Ride
    {
        $this->participations->add($participation);

        return $this;
    }

    public function removeParticipation(Participation $participation): Ride
    {
        $this->participations->removeElement($participation);

        return $this;
    }

    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addEstimate(RideEstimate $estimate): Ride
    {
        $this->estimates->add($estimate);

        return $this;
    }

    public function removeEstimate(RideEstimate $estimate): Ride
    {
        $this->estimates->removeElement($estimate);

        return $this;
    }

    public function getEstimates(): Collection
    {
        return $this->estimates;
    }

    public function addWeather(Weather $weather): Ride
    {
        $this->weathers->add($weather);

        return $this;
    }

    public function removeWeather(Weather $weathers): Ride
    {
        $this->weathers->removeElement($weathers);

        return $this;
    }

    public function getWeathers(): Collection
    {
        return $this->weathers;
    }

    public function __clone()
    {
        $this->id = null;
    }

    public function setImageFile(File $image = null): Ride
    {
        $this->imageFile = $image;

        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(string $imageName = null): PhotoInterface
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }
}
