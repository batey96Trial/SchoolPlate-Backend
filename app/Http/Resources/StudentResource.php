<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class StudentResource extends UserBaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        return array_merge(parent::toArray($request), [
                'school' => $this->school,
                'department' => $this->department,
                'level' => $this->level,
                'matricule' => $this->matricule,
                "verification_status" => $this->verification_status,
                "verification_note" => $this->verification_note,
        ]);
    }
}
