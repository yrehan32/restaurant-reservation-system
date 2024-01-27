<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User as ModelsUser;
use App\Utils\Constant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class User extends Controller
{
    public function get() : JsonResponse {
        return response()->json([
            'error' => false,
            'message' => 'User retrieved successfully.',
            'data' => [
                'user' => auth()->user(),
            ],
        ]);
    }

    public function create(CreateRequest $request) : JsonResponse
    {
        $user = ModelsUser::create($request->validated());

        $role = $user->role;

        if ($role == Constant::ADMIN_ROLE) {
            $scopes = Constant::ADMIN_SCOPE;
        } elseif ($role == Constant::USER_ROLE) {
            $scopes = Constant::USER_SCOPE;
        } else {
            $scopes = [];
        }

        $token = $user->createToken('authToken', $scopes)->accessToken;

        return response()->json([
            'error' => false,
            'message' => 'User created successfully.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function createAdmin(CreateRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $data['role'] = Constant::ADMIN_ROLE;

        $user = ModelsUser::create($data);

        $scopes = Constant::ADMIN_SCOPE;

        $token = $user->createToken('authToken', $scopes)->accessToken;

        return response()->json([
            'error' => false,
            'message' => 'Admin created successfully.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function createUser(CreateRequest $request) : JsonResponse
    {
        $user = ModelsUser::create($request->validated());

        $scopes = Constant::USER_SCOPE;

        $token = $user->createToken('authToken', $scopes)->accessToken;

        return response()->json([
            'error' => false,
            'message' => 'User created successfully.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function login(LoginRequest $request) : JsonResponse
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'error' => true,
                'message' => 'Invalid credentials.',
                'data' => null,
            ]);
        }

        $user = Auth::user();

        $role = $user->role;

        if ($role == Constant::ADMIN_ROLE) {
            $scopes = Constant::ADMIN_SCOPE;
        } elseif ($role == Constant::USER_ROLE) {
            $scopes = Constant::USER_SCOPE;
        } else {
            $scopes = [];
        }

        $token = $user->createToken('authToken', $scopes)->accessToken;

        return response()->json([
            'error' => false,
            'message' => 'User logged in successfully.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function update(UpdateRequest $request, $id) : JsonResponse
    {
        try {
            if (Auth::user()->id != $id) {
                throw new \Exception('You are not authorized to update this user.');
            }

            $user = ModelsUser::findOrFail($id);
    
            $user->update($request->validated());
    
            return response()->json([
                'error' => false,
                'message' => 'User updated successfully.',
                'data' => [
                    'user' => $user,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
                'data' => null,
            ]);
        }
    }

    public function delete($id) : JsonResponse
    {
        try {
            if (Auth::user()->id != $id) {
                throw new \Exception('You are not authorized to delete this user.');
            }
            
            $user = ModelsUser::findOrFail($id);
    
            $user->delete();
    
            return response()->json([
                'error' => false,
                'message' => 'User deleted successfully.',
                'data' => null
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage(),
                'data' => null,
            ]);
        }
    }
}
