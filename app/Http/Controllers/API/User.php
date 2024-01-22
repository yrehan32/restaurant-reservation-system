<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\User as ModelsUser;
use Illuminate\Http\JsonResponse;
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

        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'error' => false,
            'message' => 'User created successfully.',
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
