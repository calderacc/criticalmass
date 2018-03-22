<?php

namespace Criticalmass\Bundle\AppBundle\Entity;

use Criticalmass\Component\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user_user")
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class User extends BaseUser implements SocialNetworkProfileAble
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var string
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @ORM\OneToMany(targetEntity="Track", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $tracks;

    /**
     * @ORM\Column(type="smallint")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $colorRed = 0;

    /**
     * @ORM\Column(type="smallint")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $colorGreen = 0;

    /**
     * @ORM\Column(type="smallint")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected $colorBlue = 0;

    /**
     * @ORM\Column(type="boolean", options={"default" = 0})
     */
    protected $blurGalleries = false;

    /**
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="user")
     */
    protected $participations;

    /**
     * @ORM\OneToMany(targetEntity="BikerightVoucher", mappedBy="user")
     */
    protected $bikerightVouchers;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    protected $facebookId;

    /**
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    protected $facebookAccessToken;

    /**
     * @ORM\Column(name="strava_id", type="string", length=255, nullable=true)
     */
    protected $stravaId;

    /**
     * @ORM\Column(name="strava_access_token", type="string", length=255, nullable=true)
     */
    protected $stravaAccessToken;

    /**
     * @ORM\Column(name="runkeeper_id", type="string", length=255, nullable=true)
     */
    protected $runkeeperId;

    /**
     * @ORM\Column(name="runkeeper_access_token", type="string", length=255, nullable=true)
     */
    protected $runkeeperAccessToken;

    /**
     * @ORM\OneToMany(targetEntity="CityCycle", mappedBy="city", cascade={"persist", "remove"})
     */
    protected $cycles;

    public function __construct()
    {
        parent::__construct();

        $this->colorRed = rand(0, 255);
        $this->colorGreen = rand(0, 255);
        $this->colorBlue = rand(0, 255);

        $this->tracks = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->bikerightVouchers = new ArrayCollection();
    }

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\VirtualProperty
     * @JMS\SerializedName("gravatarHash")
     */
    public function getGravatarHash(): ?string
    {
        return md5($this->getEmail());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColorRed(): int
    {
        return $this->colorRed;
    }

    public function setColorRed(int $colorRed): User
    {
        $this->colorRed = $colorRed;

        return $this;
    }

    public function getColorGreen(): int
    {
        return $this->colorGreen;
    }

    public function setColorGreen(int $colorGreen): User
    {
        $this->colorGreen = $colorGreen;

        return $this;
    }

    public function getColorBlue(): int
    {
        return $this->colorBlue;
    }

    public function setColorBlue(int $colorBlue): User
    {
        $this->colorBlue = $colorBlue;

        return $this;
    }

    /**
     * @deprecated
     */
    public function equals(User $user): bool
    {
        return $user->getId() == $this->getId();
    }

    public function addTrack(Track $track): User
    {
        $this->tracks->add($track);

        return $this;
    }

    public function removeTrack(Track $tracks): User
    {
        $this->tracks->removeElement($tracks);

        return $this;
    }

    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): User
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist(): User
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();

        return $this;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate(): User
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }

    public function addParticipation(Participation $participation): User
    {
        $this->participations->add($participation);

        return $this;
    }

    public function removeParticipation(Participation $participation): User
    {
        $this->participations->removeElement($participation);

        return $this;
    }

    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function setStravaId(string $stravaId): User
    {
        $this->stravaId = $stravaId;

        return $this;
    }

    public function getStravaId(): ?string
    {
        return $this->stravaId;
    }

    public function setStravaAccessToken(string $stravaAccessToken): User
    {
        $this->stravaAccessToken = $stravaAccessToken;

        return $this;
    }

    public function getStravaAccessToken(): ?string
    {
        return $this->stravaAccessToken;
    }

    public function setFacebookId(string $facebookId): User
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookAccessToken(string $facebookAccessToken): User
    {
        $this->facebookAccessToken = $facebookAccessToken;

        return $this;
    }

    public function getFacebookAccessToken(): ?string
    {
        return $this->facebookAccessToken;
    }

    public function setRunkeeperId(string $runkeeperId): User
    {
        $this->runkeeperId = $runkeeperId;

        return $this;
    }

    public function getRunkeeperId(): ?string
    {
        return $this->runkeeperId;
    }

    public function setRunkeeperAccessToken(string $runkeeperAccessToken): User
    {
        $this->runkeeperAccessToken = $runkeeperAccessToken;

        return $this;
    }

    public function getRunkeeperAccessToken(): ?string
    {
        return $this->runkeeperAccessToken;
    }

    public function setBlurGalleries(bool $blurGalleries): User
    {
        $this->blurGalleries = $blurGalleries;

        return $this;
    }

    public function getBlurGalleries(): bool
    {
        return $this->blurGalleries;
    }

    public function isOauthAccount(): bool
    {
        return $this->runkeeperId || $this->stravaId || $this->facebookId;
    }

    public function isFacebookAccount(): bool
    {
        return $this->facebookId !== null;
    }

    public function isStravaAccount(): bool
    {
        return $this->stravaId !== null;
    }

    public function isRunkeeperAccount(): bool
    {
        return $this->facebookId !== null;
    }

    public function addBikerightVoucher(BikerightVoucher $bikerightVoucher): User
    {
        $this->bikerightVouchers->add($bikerightVoucher);

        return $this;
    }

    public function getBikerightVouchers(): Collection
    {
        return $this->bikerightVouchers;
    }

    public function removeBikerightVoucher(BikerightVoucher $bikerightVoucher): User
    {
        $this->bikerightVouchers->removeElement($bikerightVoucher);

        return $this;
    }

    public function addCycle(CityCycle $cityCycle): User
    {
        $this->cycles->add($cityCycle);

        return $this;
    }

    public function setCycles(Collection $cityCycles): User
    {
        $this->cycles = $cityCycles;

        return $this;
    }

    public function getCycles(): Collection
    {
        return $this->cycles;
    }

    public function removeCycle(CityCycle $cityCycle): User
    {
        $this->cycles->removeElement($cityCycle);

        return $this;
    }
}
