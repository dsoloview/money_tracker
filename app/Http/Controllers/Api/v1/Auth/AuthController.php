<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Data\User\UserCreateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'Auth', description: 'Authentication')]
class AuthController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    #[Endpoint('Register a new user')]
    #[ResponseFromApiResource(UserResource::class, User::class, with: ['roles', 'settings', 'settings.language', 'settings.mainCurrency'] , additional: ['access_token' => 'string', 'token_type' => 'string'])]
    public function register(UserCreateRequest $request): JsonResponse
    {
        \Log::debug('Trying to register user', ['request' => $request->email]);
        $user = $this->userService->store(UserCreateData::from($request));


        $token = $user->createToken('auth_token')->plainTextToken;

        \Log::debug('User registered', ['user' => $user->toArray()]);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user->load('roles', 'settings', 'settings.language', 'settings.mainCurrency')),
        ]);
    }

    #[Endpoint('Login a user')]
    #[ResponseFromApiResource(UserResource::class, User::class, with: ['roles', 'settings', 'settings.language', 'settings.mainCurrency'] , additional: ['access_token' => 'string', 'token_type' => 'string'])]
    public function login(LoginRequest $request): JsonResponse
    {
        \Log::debug('Trying to login user', ['request' => $request->email]);
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            throw new AuthenticationException('Invalid credentials');
        }

        $token = Auth::user()->createToken('auth_token')->plainTextToken;

        \Log::debug('User logged in', ['user' => Auth::user()->toArray()]);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource(auth()->user()->load('roles')),
        ]);

    }

    #[Endpoint('Logout a user')]
    #[Authenticated]
    #[Response(['message' => 'Tokens Revoked'])]
    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json([
            'message' => 'Tokens Revoked',
        ]);
    }
}
