<?php

namespace App\Twig;

use App\Converter\FilesizeStringConverter;
use App\Converter\MessageScopeStringConverter;
use App\Converter\StudentStringConverter;
use App\Converter\StudyGroupsGradeStringConverter;
use App\Converter\StudyGroupStringConverter;
use App\Converter\TeacherStringConverter;
use App\Converter\TimestampDateTimeConverter;
use App\Converter\UserStringConverter;
use App\Converter\UserTypeStringConverter;
use App\Entity\MessageScope;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension {

    private $teacherConverter;
    private $studentConverter;
    private $userTypeConverter;
    private $userConverter;
    private $messageScopeConverter;
    private $studyGroupConverter;
    private $studyGroupsConverter;
    private $filesizeConverter;
    private $timestampConverter;

    public function __construct(TeacherStringConverter $teacherConverter, StudentStringConverter $studentConverter,
                                UserTypeStringConverter $userTypeConverter, UserStringConverter $userConverter,
                                MessageScopeStringConverter $messageScopeConverter, StudyGroupStringConverter $studyGroupConverter,
                                StudyGroupsGradeStringConverter $studyGroupsConverter, FilesizeStringConverter $filesizeConverter,
                                TimestampDateTimeConverter $timestampConverter) {
        $this->teacherConverter = $teacherConverter;
        $this->studentConverter = $studentConverter;
        $this->userTypeConverter = $userTypeConverter;
        $this->userConverter = $userConverter;
        $this->messageScopeConverter = $messageScopeConverter;
        $this->studyGroupConverter = $studyGroupConverter;
        $this->studyGroupsConverter = $studyGroupsConverter;
        $this->filesizeConverter = $filesizeConverter;
        $this->timestampConverter = $timestampConverter;
    }

    public function getFilters() {
        return [
            new TwigFilter('teacher', [ $this, 'teacher' ]),
            new TwigFilter('student', [ $this, 'student' ]),
            new TwigFilter('usertype', [ $this, 'userType' ]),
            new TwigFilter('user', [ $this, 'user' ]),
            new TwigFilter('messagescope', [ $this, 'messageScope' ]),
            new TwigFilter('studygroup', [$this, 'studyGroup']),
            new TwigFilter('studygroups', [ $this, 'studyGroups' ]),
            new TwigFilter('filesize', [ $this, 'filesize' ]),
            new TwigFilter('todatetime', [ $this, 'toDateTime' ])
        ];
    }

    public function teacher(Teacher $teacher, bool $includeAcronym = false) {
        return $this->teacherConverter->convert($teacher, $includeAcronym);
    }

    public function student(Student $student) {
        return $this->studentConverter->convert($student);
    }

    public function userType(UserType $userType) {
        return $this->userTypeConverter->convert($userType);
    }

    public function user(User $user) {
        return $this->userConverter->convert($user);
    }

    public function messageScope(MessageScope $scope) {
        return $this->messageScopeConverter->convert($scope);
    }

    public function studyGroup(StudyGroup $group) {
        return $this->studyGroupConverter->convert($group);
    }

    public function studyGroups(iterable $studyGroups, bool $sort = false) {
        return $this->studyGroupsConverter->convert($studyGroups, $sort);
    }

    public function filesize(int $bytes) {
        return $this->filesizeConverter->convert($bytes);
    }

    public function toDateTime(int $timestamp) {
        return $this->timestampConverter->convert($timestamp);
    }
}