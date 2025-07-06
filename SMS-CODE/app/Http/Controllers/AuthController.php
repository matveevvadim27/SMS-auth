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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendCode(SendCodeRequest $request, SmsCodeService $smsCodeService)
    {

        $phone = $request->input("phone");
        $action = $request->input('action', 'login');
        $code = $smsCodeService->generateCode();

        $user = User::where('phone', $phone)->first();

        $smsCodeService->cleanExpiredCodes();

        try {
            $smsCodeService->create($user, $action, $code, $phone);
        } catch (\Exception $e) {
            Log::warning("SendCode: Ошибка генерации кода для {$phone}: {$e->getMessage()}");

            return response()->json([
                'message' => 'Вы недавно запрашивали код. Попробуйте позже.',
            ], 429);
        }

        $this->smsService->sendVerificationCode($phone, "Ваш код подтверждения: {$code}");

        return new SmsCodeResponseResource((object)[
            'phone' => $phone,
            'action' => $action,
        ]);
    }

    public function verifyCode(VerifyCodeRequest $request, SmsCodeService $smsCodeService): JsonResponse
    {

        $phone = $request->input('phone');
        $code = $request->input('code');
        $action = $request->input('action', 'login');

        $smsCode = $smsCodeService->getValidCode($phone, $action, $code);

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

        $smsCodeService->deleteCode($smsCode);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }

    public function register(RegisterRequest $request, SmsCodeService $smsCodeService)
    {

        $phone = $request->phone;
        $code = $request->code;

        $smsCode = $smsCodeService->getValidRegister($phone, $code);

        if (!$smsCode) {
            throw new HttpException(400, 'Неверный или устаревший код подтверждения');
        }

        $user = DB::transaction(function () use ($request, $phone, $smsCode, $smsCodeService) {
            $user = User::create([
                'phone' => $phone,
                'name' => $request->name,
                'role' => 2,
            ]);

            $smsCode->update(['user_id' => $user->id]);

            $smsCodeService->deleteCode($smsCode);

            return $user;
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }
}
