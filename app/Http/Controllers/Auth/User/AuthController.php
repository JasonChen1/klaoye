<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\{User,Admin};
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Create user 顾客注册
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function register(Request $request)
    {
    	$request->validate([
    		'name' => 'required|string',
    		'email' => 'required|string|email|unique:users',
    		'password' => 'required|string|confirmed'
    	]);

    	$user = User::create([
    		'name' => $request->name,
    		'email' => $request->email,
    		'password' => bcrypt($request->password)
    	]);

        $user->sendEmailVerificationNotification();

        if ($request->remember_me) {
            Passport::personalAccessTokensExpireIn(Carbon::now()->addWeeks(1));
        } else {
            Passport::personalAccessTokensExpireIn(Carbon::now()->addHours(12));
        }

    	$tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

    	return response()->json([
    		'access_token' => $tokenResult->accessToken,
    		'token_type' => 'Bearer',
    		'expires_at' => Carbon::parse(
    			$tokenResult->token->expires_at
    		)->toDateTimeString(),
    		'user' => $user
    	], 201);
    }

    /**
     * 顾客登录
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
    	$request->validate([
    		'email' => 'required|string|exists:users,email',
    		'password' => 'required|string',
    		'remember_me' => 'boolean'
    	]);
    	$credentials = request(['email', 'password']);

    	if(!Auth::attempt($credentials)){
    		return response()->json([
    			'errors' => [
    				'message'=>'Credentials Not Found'
    			]
    		], 401);
    	}

        if ($request->remember_me) {
            Passport::personalAccessTokensExpireIn(Carbon::now()->addWeeks(1));
        } else {
            Passport::personalAccessTokensExpireIn(Carbon::now()->addHours(12));
        }

    	$user = $request->user();
    	$tokenResult = $user->createToken('Personal Access Token');
    	$token = $tokenResult->token;
    	$token->save();
        $domain = $request->getHttpHost();
        $admin = Admin::where('domain',$domain)->first();
  
    	return response()->json([
    		'access_token' => $tokenResult->accessToken,
    		'token_type' => 'Bearer',
    		'expires_at' => Carbon::parse(
    			$tokenResult->token->expires_at
    		)->toDateTimeString(),
    		'user' => $user,
            'currency'=>$admin->currency??null,
    	],201);
    }

    /**
     * Logout user (Revoke the token) 顾客登出
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
    	$request->user()->token()->revoke();
    	return response()->json('Successfully logged out',204);
    }

    /**
     * Send password reset link. 
     */
    public function sendPasswordResetLink(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->json([
            'message' => 'Password reset email sent.',
            'data' => $response
        ]);
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Email could not be sent to this email address.']);
    }

}
