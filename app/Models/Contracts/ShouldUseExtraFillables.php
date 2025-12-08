<?php

namespace App\Models\Contracts;


/**
 * Interface ShouldUseExtraFillables
 *
 * This interface defines a contract for models that have extra fillable attributes
 * beyond the base `User` fillables. Implementing classes are expected to provide
 * these additional attributes via `getExtraFillables())`.
 *
 */
interface ShouldUseExtraFillables
{


    /**
     * Returns the extra fillable attributes specific to the model subclass.
     *
     * @return string[] of extra attributes allowed for mass assignment.
     */
    public function getExtraFillables(): array;
}
