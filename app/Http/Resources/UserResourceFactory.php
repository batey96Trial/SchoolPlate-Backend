<?php
namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Support\Collection;


/**
 * Custom class to produce expected resource with expected fields based on model role
 */
class UserResourceFactory {

    /**
     * @static
     * @param User $user
     * @return DonorResource|StudentResource
     */
    public static function produce(User $user){
        return match ($user->role) {
             "student" => new StudentResource($user),
             "donor" => new DonorResource($user),
             //TOD: Admin and Restaurant Resources
        };
    }

    /**
     * 
     * @param Collection<int, User>|array<int,User> $users
     * @return Collection<int,DonorResource|StudentResource> 
     */
    
    public static function collection(Collection $users):Collection{
        return $users->map(fn(User $user)=>self::produce($user));
    }
}
