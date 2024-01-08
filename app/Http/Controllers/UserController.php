<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function userRegistration(Request $request){

        try{
            $request->validate([
                'firstName'=>'required|string|max:50',
                'lastName'=>'required|string|max:50',
                'email'=>'required|string|email|max:50|unique:users,email',
                'mobile'=>'required|string|max:50',
                'password'=>'required|string|max:3',
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
                'status'=>'success',
                'message'=>'User created successfully'
            ],201);
        
        }
        catch(Exception $e){
            return response()->json([
                'status'=>'failed',
                'message'=>$e->getMessage(),
            ]);
        }  
    }

    public function userLogin(Request $request){

       try{
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
        return response()->json(['status' => 'success', 'message' => 'Login Successful','token'=>$token]);

    }catch (Exception $e){
        return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
    }
    }
    public function userProfile(){
        return Auth::user();
     }
     public function userUpdate(Request $request){
       try{
            $request->validate([
                'firstName'=>'required|string|max:50',
                'lastName'=>'required|string|max:50',
            // 'email'=>'required|string|email|max:50|unique:users,email',
                'mobile'=>'required|string|max:50',
                'password'=>'required|string|max:3',               
            ]);

            $hashedPassword = Hash::make($request->input('password'));
            $request->merge(['password' => $hashedPassword]);

            User::where('id','=',Auth::id())->update($request->only(['firstName', 'lastName', 'mobile', 'password']));
            return response()->json([
                'status'=>'success',
                'message'=>'Update success'
               ]);
       }
       catch(Exception $e){
        return response()->json([
            'status'=>'fail',
            'message'=>$e->getMessage()
        ]);
       }
     }

     function UserLogout(Request $request){
        $request->user()->tokens()->delete();
        return redirect('/user-Login');
        
    }


}
