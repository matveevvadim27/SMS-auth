<?php

namespace App\Http\Resources\User;


use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "QR_code" => $this->QR_code,
            "created_at" => $this->created_at->format('d/m/y'),
            "updated_at" => $this->updated_at,
        ];
    }
}
