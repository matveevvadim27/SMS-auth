<?php

namespace App\Services;

use App\Models\SmsCode;
use App\Models\User;

class SmsCodeService
{
    public function create(?User $user, string $action, string $code): SmsCode
    {
        return SmsCode::create([
            'user_id' => $user?->id,
            'action'  => $action,
            'code'    => $code,
        ]);
    }
}
