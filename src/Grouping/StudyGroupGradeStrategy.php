<?php

namespace App\Grouping;

use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Entity\Tuition;

class StudyGroupGradeStrategy implements GroupingStrategyInterface {

    /**
     * @param StudyGroup $object
     * @return Grade[]
     */
    public function computeKey($object) {
        return $object->getGrades()->toArray();
    }

    /**
     * @param Grade $keyA
     * @param Grade $keyB
     * @return bool
     */
    public function areEqualKeys($keyA, $keyB): bool {
        return $keyA->getId() === $keyB->getId();
    }

    /**
     * @param Grade $key
     * @return GroupInterface
     */
    public function createGroup($key): GroupInterface {
        return new StudyGroupGradeGroup($key);
    }
}