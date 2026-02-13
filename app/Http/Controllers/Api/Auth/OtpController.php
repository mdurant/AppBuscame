<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\VerificationStatus;
use App\Events\OtpRequested;
use App\Http\Controllers\Controller;
use App\Services\Auth\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otp
    ) {}

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ]);

        $user = \App\Models\User::where('email', $validated['email'])->first();
        if (! $user) {
            return response()->json(['message' => 'Código inválido o expirado.'], 422);
        }

        if (! $this->otp->verify($user, $validated['code'])) {
            return response()->json(['message' => 'Código inválido o expirado.'], 422);
        }

        $user->update(['verification_status' => VerificationStatus::FullyVerified]);

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'message' => 'Código correcto. Redirigiendo al panel.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('profile'),
        ]);
    }

    public function resend(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = \App\Models\User::where('email', $request->email)->first();
        if (! $user || $user->verification_status !== VerificationStatus::EmailVerifiedPendingOtp) {
            return response()->json(['message' => 'No se pudo renovar el código.'], 422);
        }

        $code = $this->otp->generateForUser($user);
        event(new OtpRequested($user, $code));

        return response()->json(['message' => 'Hemos enviado un nuevo código a tu correo.'], 202);
    }
}
