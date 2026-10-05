<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ForgetPasswordController extends Controller
{
    public function forgotpassword(){
        return view('common/forgot_password');
    }
    public function forgot_password_validate_email(Request $request){
      
        $request->validate([
            'email' => 'required|email',

        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response()->json(['status' => 402, 'message' => "Email is not registered in our system"]);
        }
        else{
                $mailData = [];
                $otp = implode('', array_map(function() {
                    return mt_rand(0, 9);
                }, range(1, 5)));
                $user->otp_code = $otp;
                $user->otp_created_at = date('Y-m-d H:i:s');
                $user->save();
                $mailData['otp'] = $otp;
                $mailData['username'] = $user->first_name;
                $body = view('emails.forgot_password', $mailData);
                $userEmailsSend[] = $user->email;
                // to username, to email, from username, subject, body html
                
                sendMail($user->first_name, $userEmailsSend, 'Lease Match', 'Password Reset Request', $body); // send_to_name, send_to_email, email_from_name, subject, body
                return response()->json(['status' => 200, 'message' => "otp is sent to your registered email"]);
        
        }

    }

    public function verify_otp(Request $request){
        $request->validate([
            'otp' => 'required|max:5',

        ]);
        $otp = $request->otp;
        $email = $request->email;

        $user = User::where('email', $request->email)->first();
        if($user->otp_code == null){
            return response()->json(['status' => 402, 'message' => "Invalid request"]);
        }
        if($otp == $user->otp_code){
            return response()->json(['status' => 200, 'message' => "otp validated, kindly enter your new password"]);
        }
        else{
            return response()->json(['status' => 402, 'message' => "otp mismatch, kindly use the otp we sent you"]);
            
        }
    }

    public function reset_password(Request $request){
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8', // Minimum length of 8 characters
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
                'confirmed',
            ],

        ],
        [
            'password.regex' => 'The new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        $user = User::where('email', $request->email)->first();
        if($user){
            $user->password = bcrypt($request->input('password'));
            $user->save();
            return response()->json(['status' => 200, 'message' => "Passwrd changed successfully, kindly return to login page and login again"]);

        }
        
    }
}
