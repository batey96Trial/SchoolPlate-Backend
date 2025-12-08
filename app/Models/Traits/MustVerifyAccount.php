<?php

namespace App\Models\Traits;

trait MustVerifyAccount
{
    /**
     * Determine if the Student's Account is approved.
     *
     * @return bool
     */
    public function isApproved(): bool{
        return $this->verification_status === "approved";
    }


     /**
     * Mark the given student's account as approved.
     *
     * @return bool
     */
    public function markAccountAsApproved(){
        return $this->forceFill([
            'verification_status' => "approved",
            'account_verified_at' => $this->freshTimestamp(),
        ])->save();
    }


}
