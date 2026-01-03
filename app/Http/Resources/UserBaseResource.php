<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "first_name" => $this->name,
            "last_name" => $this->surname,
            "avatar" => $this->avatar,
            "telephone" => $this->telephone,
            "role" => $this->role,
            "balance" => $this->balance
        ];
    }
}
