<?php

namespace App\Models;

use App\Models\Contracts\ShouldUseExtraFillables;
use App\Models\Traits\MustVerifyAccount;


class Student extends User implements ShouldUseExtraFillables
{
    use MustVerifyAccount;
    protected $table = "users";
    protected $extrafillables = ['school', 'department', 'level', 'matricule'];

    public function donors()
    {
        return $this->belongsToMany(Donor::class);
    }
    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

}
