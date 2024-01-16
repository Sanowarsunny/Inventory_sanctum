<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function customerPage(){
        return view('pages.dashboard.customer-page');
    }
    function listCustomer(Request $request){
        try{
            $user_id=Auth::id();
            $rows= Customer::where('user_id',$user_id)->get();
            return response()->json(['status' => 'success', 'rows' => $rows]);
        }catch (Exception $e){
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }
    function createCustomer(Request $request){

        try{
            $request->validate([
                'name' => 'required|string|max:50',
                'email' => 'required|string|email|max:50|unique:users,email',
                'mobile' => 'required|string|max:50',
            ]);
            $user_id=Auth::id();

            $data = $request->input();
            $data['user_id'] = $user_id;
            Customer::create($data);
            
            return response()->json(['status' => 'success', 'message' => "Request Successful"]);
        }catch (Exception $e){
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }
    function customerById(Request $request){
        try{
            $request->validate([
                'id' => 'required|string|min:1'
            ]);
            $customer_id = $request->input('id');
            $user_id = Auth::id();
            $rows=Customer::where('id',$customer_id)->where('user_id',$user_id)->first();
            return response()->json(['status' => 'success', 'rows' => $rows]);

        }
        catch(Exception $exception){
            return response()->json([
                'status'=>'fail',
                'message'=>$exception->getMessage()
            ]);
        }
    }
    function customerUpdate(Request $request){
        try{
            $request->validate([
                'id' => 'required|string|min:1',
                'name' => 'required|string|max:50',
                'email' => 'required|string|email|max:50',
                'mobile' => 'required|string|min:11',
            ]);
            $customer_id = $request->input('id');
            $user_id = Auth::id();
            Customer::where('id',$customer_id)
                    ->where('user_id',$user_id)
                    ->update($request->only([
                        'name',
                        'email',
                        'mobile'
                    ]));
            return response()->json(
                            ['status' => 'success', 
                            'message' => "Request Successful"
                        ]);

        }
        catch(Exception $exception){
            return response()->json([
                'status'=>'fail',
                'message'=>$exception->getMessage()
            ]);
        }
    }
    function customerDelete(Request $request){

        try{
            Customer::where('id',$request->input('id'))
            ->where('user_id',Auth::id())
            ->delete();
            return response()->json([
                    'status' => 'success', 
                    'message' => "Request Successful"
            ]);
            

        }
        catch(Exception $e){
            return response()->json([
                'status'=>'fail',
                'message'=>$e->getMessage()
            ]);
        }
    }
   
}

