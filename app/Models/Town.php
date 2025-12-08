<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Town extends Model
{

    protected $fillable = ['name'];

    public function region(): BelongsTo{
        return $this->belongsTo(Region::class);
    }
    public function users():hasMany{
        return $this->hasMany(User::class);
    }
}
