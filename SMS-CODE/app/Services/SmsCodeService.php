<?php

namespace App\Services;

use App\Models\SmsCode;
use App\Models\User;
use Carbon\Carbon;

class SmsCodeService
{
    protected int $codeExpirationMinutes = 2;

    public function generateCode(): string
    {
        return (string) rand(100000, 999999);
    }

    public function create(?User $user, string $action, string $code, string $phone): SmsCode
    {

        $recentCode = SmsCode::where('phone', $phone)
            ->where('action', $action)
            ->where('created_at', '>=', now()->subSeconds(30))
            ->latest()
            ->first();

        if ($recentCode) {
            throw new \Exception('Вы уже запрашивали код недавно. Пожалуйста, подождите немного.');
        }

        SmsCode::where('phone', $phone)
            ->where('action', $action)
            ->delete();


        return SmsCode::create([
            'user_id' => $user?->id,
            'action'  => $action,
            'code'    => $code,
            'phone'   => $phone,
        ]);
    }

    public function cleanExpiredCodes(): void
    {
        SmsCode::where('created_at', '<', now()->subMinutes($this->codeExpirationMinutes))->delete();
    }

    public function getValidCode(string $phone, string $action, int $code): ?SmsCode
    {
        return SmsCode::where('action', $action)
            ->where('code', $code)
            ->where('phone', $phone)
            ->where('created_at', '>=', Carbon::now()->subMinutes($this->codeExpirationMinutes))
            ->latest()
            ->first();
    }

    public function getValidRegister(string $phone, int $code): ?SmsCode
    {
        return SmsCode::whereNull('user_id')
            ->where('code', $code)
            ->where('phone', $phone)
            ->where('created_at', '>=', Carbon::now()->subMinutes($this->codeExpirationMinutes))
            ->latest()
            ->first();
    }


    public function deleteCode(SmsCode $code): void
    {
        $code->delete();
    }
}
