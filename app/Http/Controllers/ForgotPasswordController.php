<?php

namespace App\Http\Controllers;

use App\Models\ForgotPasswordMail;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function resetPassword(Request $request, $token)
    {
        // Check token is expired or not

        $forgotPasswordEmailObj = ForgotPasswordMail::where('token', $token)->first();

        /*if(!$forgotPasswordEmailObj || $forgotPasswordEmailObj->expired_at < date("Y-m-d H:i:s")) {
            ForgotPasswordMail::where('token', $token)->delete();
            return view('expired-mail');
        }*/

        if (!$forgotPasswordEmailObj) {
            ForgotPasswordMail::where('token', $token)->delete();
            return view('expired-mail');
        }

        $data['token'] = $token;
        $data['email'] = $request->get('email');

        return view('reset')->with(compact('data'));
    }

    public function updatePassword(Request $request)
    {
        $requestData = $request->all();

        $token = $requestData['token'];
        $tokenObj = ForgotPasswordMail::where('token', $token)->first();
        if (!$tokenObj) {
            return view('expired-mail');
        }

        $resourceObj = User::where('email', $requestData['email'])->first();

        if (!$resourceObj) {
            return redirect()->route('forgot.password')->with('error', 'Invalid request');
        }

        $userType = $resourceObj->user_type;

        $resourceObj->password = Hash::make($requestData['password']);
        if ($resourceObj->save()) {
            ForgotPasswordMail::where('token', $token)->delete();
        }

        return redirect()->route("congratulation", ['email' => $requestData['email'], 'type' => $userType]);
    }

    public function congratulation(Request $request)
    {
        $data = [];
        $requestData = $request->all();

        return view('password-reset-thanks')->with('data');
    }
}
