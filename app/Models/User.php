<?php

namespace App\Models;

use App\Models\Traits\HasUserBehaviour;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\Contracts\HasApiTokens as HasApiTokensInterface;
use Laravel\Sanctum\HasApiTokens;


/**
 * Base class for actors
 * 
 */
class User extends Authenticatable implements HasApiTokensInterface
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public function town(): BelongsTo
    {
        return $this->belongsTo(Town::class);
    }

    /**
     * Relationship for Donors with Donation
     * @return HasMany<Donation, User>
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, "donor_id");
    }

    /**
     * Relationship for Student recipients with Donation
     * @return HasMany<Donation, User>
     */
    public function recievedDonations(): HasMany
    {
        return $this->hasMany(Donation::class, "student_id");
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            if ($user->role == 'student' || $user->role == 'restaurant') {
                $user->verification_status = 'pending';
                $user->verification_note = 'To have access to all money transactions,we need to verify you.Please upload the necessary documents and our team will activate your account in the next few days';
            }
        });

        static::created(function ($user): void {
            //fire sms
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'avatar',
        'telephone',
        'town_id',
        'type',
        'balance',
        'password',
        'school',
        'department',
        'level',
        'matricule',
        'occupation',
        'role'
    ];


    /**
     * The attributes that should be hidden for serialization.217362888986-96ilnn771i6p48sbboij7i5bpgb8kmc0.apps.googleusercontent.com
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'balance' => 'decimal:2'
        ];
    }
}
