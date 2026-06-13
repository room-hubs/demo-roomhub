<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterStoreRequest;
use App\Http\Requests\Api\Auth\RegisterUpdateRequest;
use App\Service\Api\Auth\RegisterService;
use App\Service\Api\Auth\RoleService;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __construct(
        protected RegisterService $registerService,
        protected RoleService $roleService,
    ) {}
    
    // register constroller
    public function store(RegisterStoreRequest $request): JsonResponse
    {
        $result = $this->registerService->register($request->validated());

        return response()->json([
            'success'    => true,
            'message'    => $result['message'],
            'user'       => $result['user'],
            'token'      => $result['token'],       
            'needs_role' => $result['needs_role'],  
        ], 201);
    }
    // assign role to user
    public function update(RegisterUpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $role = $request->validated('role');

        $result = $this->roleService->updateRole($user, $role);

        if (! $result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'user'    => $user, // keep user so frontend stays authenticated
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'user'    => $result['user'],
            'roles'   => $result['roles'] ?? null,
            'token'   => $result['token'] ?? null,
        ]);
    }
}