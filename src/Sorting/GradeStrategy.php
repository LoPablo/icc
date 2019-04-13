<?php

namespace App\Sorting;

use App\Entity\Grade;

class GradeStrategy implements SortingStrategyInterface {

    /**
     * @param Grade $objectA
     * @param Grade $objectB
     * @return int
     */
    public function compare($objectA, $objectB): int {
        return strnatcmp($objectA->getName(), $objectB->getName());
    }
}