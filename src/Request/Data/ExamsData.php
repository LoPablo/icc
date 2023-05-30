<?php

namespace App\Request\Data;

use App\Validator\UniqueId;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ExamsData {

    use SuppressNotificationTrait;

    /**
     * This date controls at which date the imported exams begin. Only exams starting from this date (inclusive) will
     * be considered on import.
     *
     * @var DateTime|null
     */
    #[Assert\NotNull]
    #[Serializer\Type("DateTime<'Y-m-d\\TH:i:s'>")]
    private ?DateTime $startDate = null;

    /**
     * This date controls at which date the imported exams end. Only exams before this date (inclusive) will
     * be considered on import.
     *
     * @var DateTime|null
     */
    #[Assert\NotNull]
    #[Serializer\Type("DateTime<'Y-m-d\\TH:i:s'>")]
    private ?DateTime $endDate = null;

    /**
     * @var ExamData[]
     */
    #[UniqueId(propertyPath: 'id')]
    #[Assert\Valid]
    #[Serializer\Type('array<App\Request\Data\ExamData>')]
    private array $exams = [ ];

    public function getStartDate(): ?DateTime {
        return $this->startDate;
    }

    public function setStartDate(?DateTime $startDate): ExamsData {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?DateTime {
        return $this->endDate;
    }

    public function setEndDate(?DateTime $endDate): ExamsData {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return ExamData[]
     */
    public function getExams() {
        return $this->exams;
    }

    /**
     * @param ExamData[] $exams
     */
    public function setExams($exams): ExamsData {
        $this->exams = $exams;
        return $this;
    }
}