<?php

namespace App\Services\Auth;

use App\Models\Auth\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserSessionService
{
    public function createForRequest(Request $request): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $sessionId = $request->session()->getId();

        UserSession::updateOrCreate(
            [
                'user_id' => $user->id,
                'token_hash' => hash('sha256', $sessionId),
            ],
            [
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_activity_at' => now(),
            ]
        );
    }

    public function updateLastActivity(Request $request): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $sessionId = $request->session()->getId();
        UserSession::where('user_id', $user->id)
            ->where('token_hash', hash('sha256', $sessionId))
            ->update(['last_activity_at' => now()]);
    }

    public function destroyCurrent(Request $request): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $sessionId = $request->session()->getId();
        UserSession::where('user_id', $user->id)
            ->where('token_hash', hash('sha256', $sessionId))
            ->delete();
    }

    public function destroyById(int $userSessionId, Request $request): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        $userSession = UserSession::where('id', $userSessionId)->where('user_id', $user->id)->first();
        if (! $userSession) {
            return false;
        }

        if ($userSession->session_id) {
            Session::getHandler()->destroy($userSession->session_id);
        }

        $userSession->delete();

        return true;
    }

    public function isCurrentSession(Request $request, UserSession $userSession): bool
    {
        return hash_equals(
            $userSession->token_hash,
            hash('sha256', $request->session()->getId())
        );
    }
}
