<?php

namespace App\Entity;

use App\Validator\PeriodNotOverlaps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"externalId"})
 * @PeriodNotOverlaps()
 */
class TimetablePeriod {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @var string
     */
    private $externalId;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @Assert\Date()
     * @var \DateTime
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @Assert\Date()
     * @var \DateTime
     */
    private $end;

    /**
     * @ORM\ManyToMany(targetEntity="UserTypeEntity")
     * @ORM\JoinTable(name="timetable_period_visibilities",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var ArrayCollection<UserTypeEntity>
     */
    private $visibilities;

    /**
     * @ORM\OneToMany(targetEntity="TimetableLesson", mappedBy="period")
     * @var Collection<TimetableLesson>
     */
    private $lessons;


    private $supervisions;

    public function __construct() {
        $this->visibilities = new ArrayCollection();
        $this->lessons = new ArrayCollection();
        $this->supervisions = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return TimetablePeriod
     */
    public function setExternalId(?string $externalId): TimetablePeriod {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return TimetablePeriod
     */
    public function setName(?string $name): TimetablePeriod {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStart(): ?\DateTime {
        return $this->start;
    }

    /**
     * @param \DateTime|null $start
     * @return TimetablePeriod
     */
    public function setStart(?\DateTime $start): TimetablePeriod {
        $this->start = $start;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEnd(): ?\DateTime {
        return $this->end;
    }

    /**
     * @param \DateTime|null $end
     * @return TimetablePeriod
     */
    public function setEnd(?\DateTime $end): TimetablePeriod {
        $this->end = $end;
        return $this;
    }

    public function addVisibility(UserTypeEntity $visibility) {
        $this->visibilities->add($visibility);
    }

    public function removeVisibility(UserTypeEntity $visibility) {
        $this->visibilities->removeElement($visibility);
    }

    /**
     * @return Collection<UserTypeEntity>
     */
    public function getVisibilities(): Collection {
        return $this->visibilities;
    }

    /**
     * @return Collection<TimetableLesson>
     */
    public function getLessons(): Collection {
        return $this->lessons;
    }

    /**
     * @return Collection<TimetableSupervision>
     */
    public function getSupervisions(): Collection {
        return $this->supervisions;
    }
}