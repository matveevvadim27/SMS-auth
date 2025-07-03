<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendCodeRequest;
use App\Http\Requests\Auth\VerifyCodeRequest;
use App\Http\Resources\Auth\SmsCodeResponseResource;
use App\Models\SmsCode;
use App\Models\User;
use App\Services\SmsCodeService;
use App\Services\SmsService;
use Carbon\Carbon;

use Illuminate\Http\JsonResponse;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    protected SmsService $smsService;
    protected int $codeExpirationMinutes = 5;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendCode(SendCodeRequest $request, SmsCodeService $smsCodeService)
    {

        $phone = $request->input("phone");
        $action = $request->input('action', 'login');
        $code = (string)rand(100000, 999999);

        $user = User::where('phone', $phone)->first();

        $smsCodeService->create($user, $action, $code);

        $this->smsService->sendVerificationCode($phone, "Ваш код подтверждения: {$code}");

        return new SmsCodeResponseResource((object)[
            'phone' => $phone,
            'action' => $action,
        ]);
    }

    public function verifyCode(VerifyCodeRequest $request): JsonResponse
    {

        $phone = $request->input('phone');
        $code = $request->input('code');
        $action = $request->input('action', 'login');

        $smsCode = SmsCode::where('action', $action)
            ->where('code', $code)
            ->where('created_at', '>=', Carbon::now()->subMinutes($this->codeExpirationMinutes))
            ->where(function ($query) use ($phone) {
                $query->whereHas('user', function ($query) use ($phone) {
                    $query->where('phone', $phone);
                })
                    ->orWhereNull('user_id');
            })
            ->latest()
            ->first();

        if (!$smsCode) {
            throw new HttpException(400, 'Неверный или устаревший код подтверждения');
        }

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'requires_registration' => true,
                'phone_verified' => true,
            ]);
        }

        $smsCode->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }

    public function register(RegisterRequest $request)
    {

        $phone = $request->phone;
        $code = $request->code;


        $smsCode = SmsCode::whereNull('user_id')
            ->where('code', $code)
            ->where('created_at', '>=', Carbon::now()->subMinutes($this->codeExpirationMinutes))
            ->latest()
            ->first();

        if (!$smsCode) {
            throw new HttpException(400, 'Неверный или устаревший код подтверждения');
        }


        $user = User::create([
            'phone' => $phone,
            'name' => $request->name,
            'role' => 2,
        ]);


        $smsCode->update(['user_id' => $user->id]);


        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }
}
