<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DonationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->role !== "admin" ? Response::deny("Only admins can view all donations") : Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Donation $donation): Response
    {
        if ($user->id === $donation->donor_id || $user->role === "admin") {
            return Response::allow();
        }

        return Response::deny("You are not authorized to view this donation", 403);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Donation $draftdonation): Response
    {
        $recipient = User::find($draftdonation->student_id);

        if ($user->role == "student") {
            return Response::deny("Only Donors are allowed to Donate", code: 403);
        }

        // self-donation is not allowed
        if ($recipient->id === $user->id) {
            return Response::deny("You cannot donate to yourself", 403);
        }

        // Donating to another Donor is not allowed
        if ($recipient->role === "donor") {
            return Response::deny("Donation is only done on Students", 403);
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Donation $donation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Donation $donation): bool
    {
        return $user->id === $donation->donor_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Donation $donation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Donation $donation): bool
    {
        return false;
    }
}
