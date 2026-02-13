<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Auth\UserSession;
use App\Services\Auth\TwoFactorService;
use App\Services\Auth\UserSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Carbon\Carbon;

class SettingsController extends Controller
{
    public function __construct(
        protected TwoFactorService $twoFactor,
        protected UserSessionService $sessionService
    ) {}

    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('settings.profile');
    }

    public function profile(Request $request): View
    {
        $user = $request->user()->load('profile');

        $currentSession = null;
        $latestActivityAt = null;
        $sessionStartedAt = null;
        $sessions = $user->userSessions()->orderByDesc('last_activity_at')->get();
        $sessionIdHash = hash('sha256', $request->session()->getId());
        foreach ($sessions as $s) {
            if (hash_equals($s->token_hash ?? '', $sessionIdHash)) {
                $currentSession = $s;
                $sessionStartedAt = $s->created_at;
                $latestActivityAt = $s->last_activity_at;
                break;
            }
        }
        if (! $latestActivityAt && $sessions->isNotEmpty()) {
            $latestActivityAt = $sessions->first()->last_activity_at;
        }

        return view('dashboard.settings.index', [
            'user' => $user,
            'activeTab' => 'profile',
            'currentSession' => $currentSession,
            'latestActivityAt' => $latestActivityAt,
            'sessionStartedAt' => $sessionStartedAt,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', 'in:Hombre,Mujer,Prefiero no aportar'],
            'date_of_birth' => ['nullable', 'date_format:d-m-Y', 'before:today'],
        ], [
            'first_name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'Los apellidos son obligatorios.',
        ]);

        $user->update(['name' => trim($validated['first_name'].' '.$validated['last_name'])]);
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => isset($validated['date_of_birth']) ? Carbon::createFromFormat('d-m-Y', $validated['date_of_birth']) : null,
            ]
        );

        return back()->with('message', 'Perfil actualizado.');
    }

    public function destroyAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'Confirma tu contraseña para eliminar la cuenta.',
            'password.current_password' => 'La contraseña no es correcta.',
        ]);

        $user = $request->user();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $user->delete();

        return redirect()->route('home')->with('message', 'Tu cuenta ha sido eliminada.');
    }

    public function password(Request $request): View
    {
        $user = $request->user()->load('profile');

        return view('dashboard.settings.index', [
            'user' => $user,
            'activeTab' => 'password',
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()->symbols()],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $request->user()->update(['password' => $validated['password']]);

        return back()->with('message', 'Contraseña actualizada.');
    }

    public function twoFactor(Request $request): View
    {
        $user = $request->user()->load('profile');

        return view('dashboard.settings.index', [
            'user' => $user,
            'activeTab' => '2fa',
        ]);
    }

    public function enableTwoFactor(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $this->twoFactor->generateSecret($user);

        return back()->with('2fa_secret', $data['secret'])->with('2fa_qr_url', $data['qr_url']);
    }

    public function confirmTwoFactor(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ], [
            'code.required' => 'El código es obligatorio.',
            'code.size' => 'El código debe tener 6 dígitos.',
        ]);

        $user = $request->user();
        if (! $this->twoFactor->confirm($user, $request->input('code'))) {
            return back()->withErrors(['code' => 'Código inválido. Comprueba los 6 dígitos e inténtalo de nuevo.']);
        }

        $codes = $this->twoFactor->generateBackupCodes($user);

        return redirect()->route('settings.2fa')->with('message', '2FA activado. Guarda tus códigos de respaldo.')->with('backup_codes', $codes);
    }

    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'Confirma tu contraseña.',
            'password.current_password' => 'La contraseña no es correcta.',
        ]);

        $this->twoFactor->disable($request->user());

        return back()->with('message', 'Autenticación de dos factores desactivada.');
    }

    public function sessions(Request $request): View
    {
        $user = $request->user()->load('profile');
        $sessions = $user->userSessions()->orderByDesc('last_activity_at')->get();
        $currentSessionId = $request->session()->getId();

        return view('dashboard.settings.index', [
            'user' => $user,
            'activeTab' => 'sessions',
            'sessions' => $sessions,
            'currentSessionIdHash' => hash('sha256', $currentSessionId),
        ]);
    }

    public function destroySession(Request $request, UserSession $userSession): RedirectResponse
    {
        if ($userSession->user_id !== $request->user()->id) {
            abort(403);
        }

        $this->sessionService->destroyById($userSession->id, $request);

        return back()->with('message', 'Sesión terminada.');
    }
}
