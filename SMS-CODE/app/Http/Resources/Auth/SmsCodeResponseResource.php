<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmsCodeResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'status' => 'success',
            'message' => 'Код подтверждения отправлен',
            'data' => [
                'phone' => $this->phone,
                'action' => $this->action,
            ],
        ];
    }
}
