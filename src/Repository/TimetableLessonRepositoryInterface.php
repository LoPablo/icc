<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\Room;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\TimetableLesson;
use App\Entity\TimetablePeriod;
use App\View\Filter\SubjectsFilter;

interface TimetableLessonRepositoryInterface extends TransactionalRepositoryInterface {

    /**
     * @param int $id
     * @return TimetableLesson|null
     */
    public function findOneById(int $id): ?TimetableLesson;

    /**
     * @param TimetablePeriod $period
     * @param Grade $grade
     * @return TimetableLesson[]
     */
    public function findAllByPeriodAndGrade(TimetablePeriod $period, Grade $grade);

    /**
     * @param TimetablePeriod $period
     * @param Teacher $teacher
     * @return TimetableLesson[]
     */
    public function findAllByPeriodAndTeacher(TimetablePeriod $period, Teacher $teacher);

    /**
     * @param TimetablePeriod $period
     * @param Room $room
     * @return TimetableLesson[]
     */
    public function findAllByPeriodAndRoom(TimetablePeriod $period, Room $room);

    /**
     * @param TimetablePeriod $period
     * @param Student $student
     * @return TimetableLesson[]
     */
    public function findAllByPeriodAndStudent(TimetablePeriod $period, Student $student);

    /**
     * @param TimetablePeriod $period
     * @param Subject[] $subjects
     * @return TimetableLesson[]
     */
    public function findAllByPeriodAndSubjects(TimetablePeriod $period, array $subjects);

    /**
     * @return TimetableLesson[]
     */
    public function findAll();

    /**
     * @param TimetableLesson $lesson
     */
    public function persist(TimetableLesson $lesson): void;

    /**
     * @param TimetableLesson $lesson
     */
    public function remove(TimetableLesson $lesson): void;
}