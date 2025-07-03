<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    protected string $login;
    protected string $password;
    protected string $url;

    public function __construct()
    {
        $this->login = config('services.smscenter.login');
        $this->password = config('services.smscenter.password');
        $this->url = 'https://smsc.ru/sys/send.php';
    }

    public function sendVerificationCode(string $phone, string $code): bool
    {
        $response = Http::post($this->url, [
            'login' => $this->login,
            'psw' => $this->password,
            'phones' => $phone,
            'mes' => "Ваш код подтверждения: {$code}",
            'fmt' => 3
        ]);

        return $response->successful();
    }
}
