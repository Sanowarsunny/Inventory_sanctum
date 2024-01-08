<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            $request->pass(['password' => $hashedPassword]);

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



}
