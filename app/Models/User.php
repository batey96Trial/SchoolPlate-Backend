<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Traits\HasUserBehaviour;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\Contracts\HasApiTokens as HasApiTokensInterface;
use Laravel\Sanctum\HasApiTokens;


 /**
  * Base class for actors
  * 
  */
  class  User extends Authenticatable implements HasApiTokensInterface
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    public function town():BelongsTo{
        return $this->belongsTo(Town::class);
    }

    protected static function boot(){
        parent::boot();
        static::creating(function($user){
            if ($user->role == 'student' || $user->role == 'restaurant' ) {
                $user->verification_status = 'pending';
                $user->verification_note = 'To have access to all money transactions,we need to verify you.Please upload the necessary documents and our team will activate your account in the next few days';
            }
        });

        static::created(function($user):void{
            //fire sms
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'avatar',
        'telephone',
        'town_id',
        'type',
        'balance',
        'email',
        'password',
        'school',
        'department',
        'level',
        'matricule',
        'occupation',
        'role'
     ];


    /**
     * The attributes that should be hidden for serialization.
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2'
        ];
    }
}
