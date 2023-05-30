<?php

namespace App\Import;

use App\Entity\GradeTeacher;
use App\Entity\GradeTeacherType;
use App\Repository\GradeRepositoryInterface;
use App\Repository\GradeTeacherRepositoryInterface;
use App\Repository\SectionRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\GradeTeacherData;
use App\Request\Data\GradeTeachersData;

class GradeTeachersImportStrategy implements ReplaceImportStrategyInterface {

    public function __construct(private GradeTeacherRepositoryInterface $gradeTeacherRepository, private GradeRepositoryInterface $gradeRepository, private TeacherRepositoryInterface $teacherRepository, private SectionRepositoryInterface $sectionRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->gradeTeacherRepository;
    }

    /**
     * @param GradeTeachersData $data
     * @throws SectionNotFoundException
     */
    public function removeAll($data): void {
        $section = $this->sectionRepository->findOneByNumberAndYear($data->getSection(), $data->getYear());

        if($section === null) {
            throw new SectionNotFoundException($data->getSection(), $data->getYear());
        }

        $this->gradeTeacherRepository->removeAll($section);
    }

    /**
     * @param GradeTeacherData $data
     * @param GradeTeachersData $requestData
     * @throws ImportException
     */
    public function persist($data, $requestData): void {
        $teacher = $this->teacherRepository->findOneByAcronym($data->getTeacher());

        if($teacher === null) {
            throw new ImportException(sprintf('Lehrkraft mit ID "%s" wurde nicht gefunden.', $data->getTeacher()));
        }

        $grade = $this->gradeRepository->findOneByExternalId($data->getGrade());

        if($grade === null) {
            throw new ImportException(sprintf('Klasse "%s" wurde nicht gefunden.', $data->getGrade()));
        }

        $section = $this->sectionRepository->findOneByNumberAndYear($requestData->getSection(), $requestData->getYear());

        if($section === null) {
            throw new SectionNotFoundException($requestData->getSection(), $requestData->getYear());
        }

        $gradeTeacher = (new GradeTeacher())
            ->setTeacher($teacher)
            ->setGrade($grade)
            ->setType(GradeTeacherType::from($data->getType()))
            ->setSection($section);

        $this->gradeTeacherRepository->persist($gradeTeacher);
    }

    /**
     * @param GradeTeachersData $data
     * @return GradeTeacherData[]
     */
    public function getData($data): array {
        return $data->getGradeTeachers();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return GradeTeacher::class;
    }
}