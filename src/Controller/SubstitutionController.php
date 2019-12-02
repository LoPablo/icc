<?php

namespace App\Controller;

use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\Substitution;
use App\Entity\User;
use App\Grouping\Grouper;
use App\Repository\SubstitutionRepositoryInterface;
use App\Settings\SubstitutionSettings;
use App\Sorting\Sorter;
use App\Sorting\SubstitutionStrategy;
use App\View\Filter\GradeFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use App\View\Parameter\GroupByParameter;
use App\View\Parameter\ViewParameter;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Routing\Annotation\Route;

class SubstitutionController extends AbstractControllerWithMessages {

    private const SectionKey = 'substitutions';

    /**
     * @Route("/substitutions", name="substitutions")
     */
    public function index(SubstitutionRepositoryInterface $substitutionRepository, StudentFilter $studentFilter,
                          GradeFilter $gradeFilter, TeacherFilter $teacherFilter, GroupByParameter $groupByParameter, ViewParameter $viewParameter,
                          Grouper $grouper, Sorter $sorter, DateHelper $dateHelper, SubstitutionSettings $substitutionSettings,
                          ?string $date, ?int $studentId = null, ?int $gradeId = null, ?string $teacherAcronym = null, ?string $groupBy = null, ?string $view = null) {
        /** @var User $user */
        $user = $this->getUser();
        $days = $this->getListOfNextDays($dateHelper, $substitutionSettings->getNumberOfAheadDaysForSubstitutions(), $substitutionSettings->skipWeekends());
        $selectedDate = $this->getCurrentDate($days, $date);

        $studentFilterView = $studentFilter->handle($studentId, $user);
        $gradeFilterView = $gradeFilter->handle($gradeId, $user);
        $teacherFilterView = $teacherFilter->handle($teacherAcronym, $user);

        /** @var Substitution[] $substitutions */
        $substitutions = [ ];

        if($teacherFilterView->getCurrentTeacher() !== null) {
            $substitutions = $substitutionRepository->findAllForTeacher($teacherFilterView->getCurrentTeacher(), $selectedDate);
        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $substitutions = $substitutionRepository->findAllForGrade($gradeFilterView->getCurrentGrade(), $selectedDate);
        } else if($studentFilterView->getCurrentStudent() !== null) {
            $studyGroups = array_map(function(StudyGroupMembership $membership) {
                return $membership->getStudyGroup();
            }, $studentFilterView->getCurrentStudent()->getStudyGroupMemberships()->toArray());

            $substitutions = $substitutionRepository->findAllForStudyGroups($studyGroups, $selectedDate);
        } else {
            $substitutions = $substitutionRepository->findAllByDate($selectedDate);
        }

        $groupingClass = $groupByParameter->getGroupingStrategyClassName($groupBy, $user, static::SectionKey);
        $groups = $grouper->group($substitutions, $groupingClass);

        $sortingClass = $groupByParameter->getSortingStrategyClassName($groupingClass);

        $sorter->sort($groups, $sortingClass);
        $sorter->sortGroupItems($groups, SubstitutionStrategy::class);

        $viewType = $viewParameter->getViewType($view, $user, static::SectionKey);

        return $this->renderWithMessages('substitutions/index.html.twig', [
            'groups' => $groups,
            'days' => $days,
            'selectedDate' => $selectedDate,
            'studentFilter' => $studentFilterView,
            'gradeFilter' => $gradeFilterView,
            'teacherFilter' => $teacherFilterView,
            'view' => $viewType,
            'groupBy' => $groupByParameter->getGroupingStrategyKey($groupingClass)
        ]);
    }

    private function getListOfNextDays(DateHelper $dateHelper, int $numberOfDays, bool $skipWeekends) {
        $today = $dateHelper->getToday();

        if($skipWeekends) {
            // Ensure to start at a weekday in case weekends are skipped
            while ($today->format('N') >= 6) {
                $today->modify('+1 day');
            }
        }

        $days = [ $today ];
        $last = $today;

        while(count($days) < $numberOfDays) {
            $day = clone $last;
            $day->modify('+1 day');

            if($skipWeekends === false || $day->format('N') < 6) {
                $days[] = $day;
            }

            $last = $day;
        }

        return $days;
    }

    /**
     * @param \DateTime[] $dateTimes
     * @param string|null $date
     * @return \DateTime|null
     */
    private function getCurrentDate(array $dateTimes, ?string $date): ?\DateTime {
        if(count($dateTimes) === 0) {
            return null;
        }

        if($date === null) {
            return $dateTimes[0];
        }

        $selectedDateTime = new \DateTime($date);

        foreach($dateTimes as $dateTime) {
            if($dateTime == $selectedDateTime) {
                return $dateTime;
            }
        }

        return $dateTimes[0];
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Substitutions();
    }
}