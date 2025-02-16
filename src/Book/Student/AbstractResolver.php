<?php

namespace App\Book\Student;

use App\Entity\ExcuseNote;
use App\Entity\LessonAttendance as LessonAttendanceEntity;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\Repository\LessonAttendanceRepositoryInterface;
use App\Settings\TimetableSettings;

abstract class AbstractResolver {
    public function __construct(private readonly LessonAttendanceRepositoryInterface $attendanceRepository, private readonly ExcuseNoteRepositoryInterface $excuseNoteRepository, private readonly ExcuseCollectionResolver $excuseCollectionResolver)
    {
    }

    protected function getAttendanceRepository(): LessonAttendanceRepositoryInterface {
        return $this->attendanceRepository;
    }

    protected function getExcuseNoteRepository(): ExcuseNoteRepositoryInterface {
        return $this->excuseNoteRepository;
    }

    /**
     * @param ExcuseNote[] $excuseNotes
     * @return ExcuseCollection[]
     */
    protected function computeExcuseCollections(array $excuseNotes): array {
        // for compatibility reasons only -> merge with subclasses!
        return $this->excuseCollectionResolver->resolve($excuseNotes);
    }

    /**
     * @param LessonAttendanceEntity[] $attendances
     * @return LessonAttendance[]
     */
    protected function computeAttendanceCollectionWithoutExcuses(array $attendances): array {
        $lessonAttendance = [ ];

        foreach($attendances as $attendance) {
            $excuses = new ExcuseCollection($attendance->getEntry()->getLesson()->getDate(), $attendance->getEntry()->getLessonStart());
            $lessonAttendance[] = new LessonAttendance($attendance->getEntry()->getLesson()->getDate(), $attendance->getEntry()->getLessonStart(), $attendance, $excuses);
        }

        return $lessonAttendance;
    }

    /**
     * @param LessonAttendanceEntity[] $attendances
     * @param ExcuseCollection[] $excuseCollection
     * @return LessonAttendance[]
     */
    protected function computeAttendanceCollection(array $attendances, array $excuseCollection): array {
        $lessonAttendance = [ ];

        foreach($attendances as $attendance) {
            for($lesson = $attendance->getEntry()->getLessonStart(); $lesson <= $attendance->getEntry()->getLessonEnd(); $lesson++) {
                $key = sprintf('%s-%d', $attendance->getEntry()->getLesson()->getDate()->format('Y-m-d'), $lesson);

                $excuses = new ExcuseCollection($attendance->getEntry()->getLesson()->getDate(), $lesson);

                if(isset($excuseCollection[$key])) {
                    $excuses = $excuseCollection[$key];
                }

                $lessonAttendance[] = new LessonAttendance($attendance->getEntry()->getLesson()->getDate(), $lesson, $attendance, $excuses);
            }
        }

        return $lessonAttendance;
    }
}