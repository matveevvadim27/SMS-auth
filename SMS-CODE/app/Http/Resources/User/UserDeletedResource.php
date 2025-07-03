<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDeletedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "phone" => $this->phone,
            "role" => $this->role,
            "created_at" => $this->created_at->format('d/m/y'),
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at->format('d/m/y'),
        ];
    }
}
