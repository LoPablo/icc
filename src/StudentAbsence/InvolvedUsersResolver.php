<?php

namespace App\StudentAbsence;

use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Entity\StudentAbsence;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Utils\ArrayUtils;
use SchulIT\CommonBundle\Helper\DateHelper;

class InvolvedUsersResolver {

    public function __construct(private readonly SectionResolverInterface $sectionResolver,
                                private readonly UserRepositoryInterface $userRepository,
                                private readonly DateHelper $dateHelper) {

    }

    /**
     * @param StudentAbsence $absence
     * @return User[]
     */
    public function resolveUsers(StudentAbsence $absence) {
        return array_values(
            ArrayUtils::createArrayWithKeys(
                array_merge(
                    [$absence->getCreatedBy()],
                    $this->resolveGradeTeachers($absence),
                    $this->resolveParents($absence),
                    $this->resolveFullAgedStudents($absence)
                ),
                fn(User $user) => $user->getId()
            )
        );
    }

    /**
     * @param StudentAbsence $absence
     * @return User[]
     */
    public function resolveGradeTeachers(StudentAbsence $absence): array {
        $teachers = [ ];
        $section = $this->sectionResolver->getSectionForDate($absence->getFrom()->getDate());
        /** @var Grade|null $grade */
        $grade = $absence->getStudent()->getGrade($section);
        if($grade !== null && $section !== null) {
            /** @var GradeTeacher $teacher */
            foreach ($grade->getTeachers() as $teacher) {
                if($teacher->getSection()->getId() === $section->getId()) {
                    $teachers[] = $teacher->getTeacher();
                }
            }
        }

        return $this->userRepository->findAllTeachers($teachers);
    }

    /**
     * @param StudentAbsence $absence
     * @return User[]
     */
    public function resolveParents(StudentAbsence $absence): array {
        if($absence->getStudent() === null) {
            return [ ];
        }

        return $this->userRepository->findAllParentsByStudents([$absence->getStudent()]);
    }

    /**
     * @param StudentAbsence $absence
     * @return User[]
     */
    public function resolveFullAgedStudents(StudentAbsence $absence): array {
        if($absence->getStudent() === null) {
            return [ ];
        }

        if($absence->getStudent()->isFullAged($this->dateHelper->getToday()) !== true) {
            return [ ];
        }

        return $this->userRepository->findAllStudentsByStudents([$absence->getStudent()]);
    }
}