<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;
use Validator;


class PassportAuthController extends Controller
{
    /**
     * Register new users.
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()],
                HTTPResponse::HTTP_UNAUTHORIZED);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('Laravel9PassportAuth')->accessToken;

        return response()->json(['status' => 'success', 'message' => "User registration completed.", 'token' => $token]
            , HTTPResponse::HTTP_OK);
    }

    /**
     * Validate Login details.
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()],
                HTTPResponse::HTTP_UNAUTHORIZED);
        }
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('Laravel9PassportAuth')->accessToken;

            return response()->json(['status' => 'success', 'message' => "Login successful.", 'token' => $token],
                HTTPResponse::HTTP_OK);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Unauthorised'],
                HTTPResponse::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Returns Logged-in users information.
     * @return JsonResponse
     */
    public function userInfo(): JsonResponse
    {
        $user = auth()->user();

        return response()->json(['status' => 'success', 'message' => 'User details.', 'user' => $user],
            HTTPResponse::HTTP_OK);

    }
}
