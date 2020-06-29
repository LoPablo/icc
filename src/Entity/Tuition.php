<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"externalId"})
 */
class Tuition {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true)
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
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     *
     * @var string|null
     */
    private $displayName = null;

    /**
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Subject
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity="Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Teacher
     */
    private $teacher;

    /**
     * @ORM\ManyToMany(targetEntity="Teacher")
     * @ORM\JoinTable(name="tuition_additional_teachers",
     *     joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @var Collection<Teacher>
     */
    private $additionalTeachers;

    /**
     * @ORM\ManyToOne(targetEntity="StudyGroup", inversedBy="tuitions")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var StudyGroup
     */
    private $studyGroup;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->additionalTeachers = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getExternalId(): string {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     * @return Tuition
     */
    public function setExternalId(string $externalId): Tuition {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Tuition
     */
    public function setName(string $name): Tuition {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string {
        return $this->displayName;
    }

    /**
     * @param string|null $displayName
     * @return Tuition
     */
    public function setDisplayName(?string $displayName): Tuition {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return Subject
     */
    public function getSubject(): Subject {
        return $this->subject;
    }

    /**
     * @param Subject $subject
     * @return Tuition
     */
    public function setSubject(Subject $subject): Tuition {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher|null $teacher
     * @return Tuition
     */
    public function setTeacher(?Teacher $teacher): Tuition {
        $this->teacher = $teacher;
        return $this;
    }

    public function addAdditionalTeacher(Teacher $teacher) {
        $this->additionalTeachers->add($teacher);
    }

    public function removeAdditionalTeacher(Teacher $teacher) {
        $this->additionalTeachers->removeElement($teacher);
    }

    /**
     * @return Collection<Teacher>
     */
    public function getAdditionalTeachers(): Collection {
        return $this->additionalTeachers;
    }

    /**
     * @return StudyGroup
     */
    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }

    /**
     * @param StudyGroup $studyGroup
     * @return Tuition
     */
    public function setStudyGroup(StudyGroup $studyGroup): Tuition {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers() {
        return array_merge(
            [ $this->getTeacher() ],
            $this->getAdditionalTeachers()->toArray()
        );
    }

    public function __toString() {
        return sprintf('%s [%s]', $this->getStudyGroup(), $this->getSubject());
    }
}