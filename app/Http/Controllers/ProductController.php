<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function productPage():View{
        return view('pages.dashboard.product-page');
    }
    public function listProducts(){
        try{
             $user_id = Auth::id();
            $rows = Product::where('user_id',$user_id)->get();
            return response()->json(
                [
                    'status'=>"success",
                    'message'=>"Request Success",
                    'rows'=>$rows
                ]
            );
        }
        catch(Exception $e){
            return response()->json(
                [
                    'status'=>"Fail",
                    'message'=>$e->getMessage(),

                ]
            );
        }
    }

    
}
