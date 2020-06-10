<?php

namespace App\Repository;

use App\Entity\Exam;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\Tuition;
use DateTime;

interface ExamRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return Exam|null
     */
    public function findOneById(int $id): ?Exam;

    /**
     * @param string $externalId
     * @return Exam|null
     */
    public function findOneByExternalId(string $externalId): ?Exam;

    /**
     * @param Tuition[] $tuitions
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @return Exam[]
     */
    public function findAllByTuitions(array $tuitions, ?\DateTime $today = null);

    /**
     * @param StudyGroup $studyGroup
     * @param DateTime|null $today
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @return Exam[]
     */
    public function findAllByStudyGroup(StudyGroup $studyGroup, ?DateTime $today = null, bool $onlyToday = false);

    /**
     * @param StudyGroup $studyGroup
     * @param DateTime|null $today
     * @return array
     */
    public function findAllDatesByStudyGroup(StudyGroup $studyGroup, ?DateTime $today = null);

    /**
     * @param Teacher $teacher
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @return Exam[]
     */
    public function findAllByTeacher(Teacher $teacher, ?\DateTime $today = null, bool $onlyToday = false);

    /**
     * @param Teacher $teacher
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @return array
     */
    public function findAllDatesByTeacher(Teacher $teacher, ?\DateTime $today = null, bool $onlyToday = false);

    /**
     * @param Student[] $students
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @return Exam[]
     */
    public function findAllByStudents(array $students, ?\DateTime $today = null, bool $onlyToday = false);

    /**
     * @param Student[] $students
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @return array
     */
    public function findAllDatesByStudents(array $students, ?\DateTime $today = null, bool $onlyToday = false);

    /**
     * @param Grade $grade
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @return Exam[]
     */
    public function findAllByGrade(Grade $grade, ?\DateTime $today = null, bool $onlyToday = false);

    /**
     * @param Grade $grade
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @return array
     */
    public function findAllDatesByGrade(Grade $grade, ?\DateTime $today = null, bool $onlyToday = false);

    /**
     * @param \DateTime $today
     * @param int $lesson
     * @return Exam[]
     */
    public function findAllByDateAndLesson(\DateTime $today, int $lesson): array;

    /**
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @param bool $onlyToday If set to true, only return exams for the given $today date
     * @return Exam[]
     */
    public function findAll(?\DateTime $today = null, bool $onlyToday = false);

    /**
     * @param \DateTime|null $today
     * @return Exam[]
     */
    public function findAllDates(?\DateTime $today = null);

    /**
     * @param \DateTime|null $today If set, only exams on $today or later are returned
     * @return Exam[]
     */
    public function findAllExternal(\DateTime $today = null);

    /**
     * @param Exam $exam
     */
    public function persist(Exam $exam): void;

    /**
     * @param Exam $exam
     */
    public function remove(Exam $exam): void;
}