<?php

namespace Sourcya\BoilerplateBox\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sourcya\BoilerplateBox\Http\Controllers\Controller;
use Sourcya\BoilerplateBox\Http\Resources\UserResource;
use Sourcya\UserModule\Models\UserProxy;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PassportController extends Controller
{
    /**
     * PassportController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api',['only' => ['logout','logoutAllDevices']]);
        $this->middleware('throttle:5,1');
    }
    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate(
            [
                'first_name' => 'required|string|max:191',
                'last_name' => 'required|string|max:191',
                'email' => 'required|string|email|max:191|unique:user',
                'password' => 'required|string|min:6|confirmed',
            ]
        );

        // Creating new user
        $user = UserProxy::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $client_role = Role::where('name','Client')->first();
        $user->assignRole($client_role);

        // Creating new access token for the user
        $token = $user->createToken('Personal Access Token')->accessToken;

        if (!empty($token)) {
            return response()->json([
                'token' => $token,
                'status' => 'success',
                'message' => 'Successful user registration'
            ], 201);
        } else {
            $message = 'Internal server error';
            return $this->errorResponseHandler($message,500);
        }
    }

    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::guard('web')->attempt($credentials, false, false)) {
            $token = Auth::guard('web')->user()->createToken('Personal Access Token')->accessToken;
            if (!empty($token)) {
                return response()->json([
                    'token' => $token,
                    'data' => new UserResource(Auth::guard('web')->user()),
                    'status' => 'success',
                    'message' => 'Successful user login, Redirect now.'
                ], 200);
            } else {
                $message = 'Internal server error';
                return $this->errorResponseHandler($message,500);
            }
        } else {
            $message = 'Invalid username/password supplied';
            return $this->errorResponseHandler($message,422);
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return \Illuminate\Http\JsonResponse [string] message
     */
    public function logout()
    {
        if(auth()->user()->token()->revoke()){
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out'
            ]);
        } else {
            $message = 'Internal server error';
            return $this->errorResponseHandler($message,500);
        }
    }
/**
     * Logout user (Revoke the token)
     *
     * @return \Illuminate\Http\JsonResponse [string] message
     */
    public function logoutAllDevices()
    {
        $logoutChk = DB::table('oauth_access_tokens')
            ->where('user_id', auth()->id())
            ->update([
                'revoked' => true
            ]);
        if($logoutChk){
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out from all devices'
            ]);
        } else {
            $message = 'Internal server error';
            return $this->errorResponseHandler($message,500);
        }
    }

    //...
}
