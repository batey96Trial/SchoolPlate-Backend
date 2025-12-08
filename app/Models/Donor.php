<?php

namespace App\Models;

use App\Models\Contracts\ShouldUseExtraFillables;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;


class Donor extends User implements ShouldUseExtraFillables
{

    protected $table = "users";
    protected $extrafillables = ['occupation'];

    public function donations():HasOneOrMany{
        return $this->hasMany(Donation::class);
    }
    public function students(){
        return $this->hasMany(Student::class);
    }
}
