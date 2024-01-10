<?php

namespace App\Http\Controllers;

use App\Mail\OTPEmail;
use App\Models\User;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    function LoginPage():View{
        return view('pages.auth.login-page');
    }
    public function userLogin(Request $request)
    {

        try {
            $request->validate([
                'email' => 'required|string|email|max:50',
                'password' => 'required|string|min:3'
            ]);

            $user = User::where('email', $request->input('email'))->first();

            //dd($user);

            if (!$user || !Hash::check($request->input('password'), $user->password)) {
                return response()->json(['status' => 'failed', 'message' => 'Invalid User']);
            }

            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Login Successful',
                'token' => $token
            ],200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function Profile()
    {
        return view('pages.dashboard.profile-page');
    }
    function userProfile(Request $request){
        return Auth::user();
    }
    function RegistrationPage():View{
        return view('pages.auth.registration-page');
    }
    function SendOtpPage():View{
        return view('pages.auth.send-otp-page');
    }
    function VerifyOTPPage():View{
        return view('pages.auth.verify-otp-page');
    }

    function ResetPasswordPage():View{
        return view('pages.auth.reset-pass-page');
    }



    public function userRegistration(Request $request)
    {

        try {
            $request->validate([
                'firstName' => 'required|string|max:50',
                'lastName' => 'required|string|max:50',
                'email' => 'required|string|email|max:50|unique:users,email',
                'mobile' => 'required|string|max:50',
                'password' => 'required|string|max:3',
            ]);

            $hashedPassword = Hash::make($request->input('password'));
            /*
            The merge method in Laravel's Request class is used to merge additional 
            input into the request's input data. It's commonly used to add or override values 
            in the request data before further processing.This line is replacing the original 'password' value 
            in the request data with the hashed password. It ensures that the hashed password 
            is used when creating the User instance in the User::create($request->input()) line.
            */
            $request->merge(['password' => $hashedPassword]);

            User::create($request->input());
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage(),
            ]);
        }
    }
    
    
    public function userUpdate(Request $request)
    {
        try {
            $request->validate([
                'firstName' => 'required|string|max:50',
                'lastName' => 'required|string|max:50',
                // 'email'=>'required|string|email|max:50|unique:users,email',
                'mobile' => 'required|string|max:50',
                'password' => 'required|string|max:3',
            ]);

            $hashedPassword = Hash::make($request->input('password'));
            $request->merge(['password' => $hashedPassword]);

            User::where('id', '=', Auth::id())->update($request->only(['firstName', 'lastName', 'mobile', 'password']));
            return response()->json([
                'status' => 'success',
                'message' => 'Update success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function sendOTP(Request $request)
    {

        $email = $request->input('email');

        $otp = rand(100000, 999999);

        $count = User::where('email', '=', $email)->count();

        if ($count == 1) {

            Mail::to($email)->send(new OTPEmail($otp));

            User::where('email', '=', $email)->update(['otp' => $otp]);
            return response()->json([
                'status' => "success",
                'message' => "Authorized",
                'otp' => $otp
            ], 200);
        } else {
            return response()->json([
                'status' => "OTP Failed",
                'message' => "Unauthorized"
            ], 401);
        }
    }
    function verifyOTP(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email|max:50',
                'otp' => 'required|string|max:6'
            ]);
            $email = $request->input('email');
            $otp = $request->input('otp');

            $count = User::where('email', '=', $email)
                ->where('otp', '=', $otp)->first();

            if ($count) {

                User::where('email', '=', $email)->update(['otp' => 0]); // otp field value 0

                $token = $count->createToken('authToken')->plainTextToken;

                return response()
                    ->json(
                        [
                            'status' => 'success',
                            'message' => " OTP verify Success",
                            'token' => $token
                        ],
                        200
                    );
            } else {
                return response()->json([
                    'status' => 'OTP verify fail',
                    'message' => "verify OTP fail"
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => " Failed",
                'message' => $e->getMessage()
            ]);
        }
    }

    function resetPassword(Request $request)
    {
        try {

            $request->validate([
                'password' => 'required|string|min:3'
            ]);

            $id = Auth::id();
            $password = $request->input('password');

            User::where('id', '=', $id)->update(['password' => Hash::make($password)]);

            return response()
                ->json(
                    [
                        'status' => 'success',
                        'message' => " Reset password  Success",
                    ],200);
        } 
        catch (Exception $e) {
            return response()->json([
                'status' => " Failed",
                'message' => $e->getMessage()
            ]);
        }
    }
    function UserLogout(Request $request)
    {
        $request->user()->tokens()->delete();
        return redirect('/userLogin');
    }
}
