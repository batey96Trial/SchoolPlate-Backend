<?php

namespace App\Models\Traits;
use Illuminate\Database\Eloquent\Builder;

trait HasUserBehaviour
{

    /**
     * Trait HasUserBehaviour
     *
     * Provides shared behavior for models that:
     * - Are a type of User (e.g., Donor, Child)
     * - Require automatic merging of extra fillable attributes
     * - Automatically set the `type` attribute based on the subclass
     *
     */
    protected static function bootHasUserBehaviour()
    {
        if (class_basename(static::class) !== 'User') {
            static::addGlobalScope("user_role", function (Builder $query) {
            $query->where('role', strtolower(class_basename(static::class)));
        });
        }

        static::creating(function ($model) {
            $model->role = strtolower(class_basename(static::class));
            if ($model->role == 'student') {
                $model->verification_status = 'pending';

            }
        });
    }


    /**
     * Merge base fillables with extra fillables if defined on the model.
     *
     *  @return string[]
     */
    public function getFillable(): array
    {
        $fillable = parent::getFillable();
        if ($this instanceof \App\Models\Contracts\ShouldUseExtraFillables) {
            $fillable = array_merge($fillable, $this->getExtraFillables());
        }
        return $fillable;
    }
    public function getExtraFillables(): array
    {
        return $this->extrafillables;
    }
}
