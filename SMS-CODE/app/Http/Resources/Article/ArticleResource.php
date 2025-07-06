<?php

namespace App\Http\Resources\Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            "status" => $this->status,
            "visibility" => $this->visibility,
            "QR_code" => $this->QR_code,
            "created_at" => $this->created_at->format('d/m/y'),
            "updated_at" => $this->updated_at,
        ];
    }
}
