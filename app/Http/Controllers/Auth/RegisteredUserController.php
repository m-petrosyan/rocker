<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EmailVerificationSendRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Models\User;
use App\Services\UserRegisterService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    protected UserRegisterService $userRegisterService;

    public function __construct(UserRegisterService $userRegisterService)
    {
        $this->userRegisterService = $userRegisterService;
    }

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     */
    public function store(RegisterRequest $request)
    {
        $this->userRegisterService->store($request->validated());

//        $this->resend($request['email']);

        return redirect()->route('login');
    }

    /**
     * @param  string  $email
     * @return JsonResponse
     */
    public function resend(string $email): JsonResponse
    {
        $user = User::where('email', $email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 400);
        } elseif ($user->verification->count() >= 3
            && $user->verification->sortByDesc('id')->first()->created_at->isToday()) {
            return response()->json(['message' => 'Maximum resend limit reached. Try tomorrow'], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent.'], 200);
    }
}
