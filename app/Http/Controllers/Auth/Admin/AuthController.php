<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Carbon\Carbon;
use App\Models\Admin;
use Laravel\Passport\Passport;

class AuthController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Login user and create token 管理员登录
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
    		'email' => 'required|string|exists:admins,email',
    		'password' => 'required|string',
    		'remember_me' => 'boolean'
    	]);

        Admin::where([['email',$request->email]])->firstOrFail();
    	$credentials = request(['email', 'password']);

        if(!Auth::guard('admin')->attempt($credentials)){
    		return response()->json([
    			'errors' => [
    				'message'=>'Credentials Not Found'
    			]
    		], 401);
    	}

        if ($request->remember_me) {
            Passport::personalAccessTokensExpireIn(Carbon::now()->addWeeks(1));
        } else {
            Passport::personalAccessTokensExpireIn(Carbon::now()->addHours(2));
        }

    	$admin = Auth::guard('admin')->user();
    	$tokenResult = $admin->createToken('Personal Access Token');
    	$token = $tokenResult->token;
    	$token->save();

    	return response()->json([
    		'access_token' => $tokenResult->accessToken,
    		'token_type' => 'Bearer',
    		'expires_at' => Carbon::parse(
    			$tokenResult->token->expires_at
    		)->toDateTimeString(),
    		'admin' => $admin,
    	],201);
    }

    /**
     * 刷新登录token - token 只能保持2小时有效期
     *
     */
    public function refreshToken(Request $request){
        $admin = $request->user();
        Passport::personalAccessTokensExpireIn(Carbon::now()->addHours(2));
        $tokenResult = $admin->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
        ],201);
    }

    /**
     * Logout user (Revoke the token) 注销用户（撤消令牌）
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
    	$request->user()->token()->revoke();
    	return response()->json('Successfully logged out',204);
    }

    /**
     * Send password reset link. 发送密码重置链接。
     */
    public function sendPasswordResetLink(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }

    /**
     * Get the response for a successful password reset link. 成功发送邮件返回响应
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
     * Get the response for a failed password reset link. 发送邮件错误返回响应
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return response()->json(['message' => 'Email could not be sent to this email address.']);
    }

    // 登录admin协议
    public function broker()
    {
        return Password::broker('admins');
    }
}