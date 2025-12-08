<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{

        protected $fillable = ['name'];

    use HasFactory;
    public function towns(): HasMany{
        return $this->hasMany(Town::class);
    }
}
