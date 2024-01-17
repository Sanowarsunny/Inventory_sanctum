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
            return response()->json(['status' => 'success', 'rows' => $rows]);
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

    public function createProduct(Request $request){
        //$user_id=$request->header('id');
        $user_id = Auth::id();
        //dd($user_id);
        // Prepare File Name & Path
        $img=$request->file('img');

        $t=time();
        $file_name=$img->getClientOriginalName();
        $img_name="{$user_id}-{$t}-{$file_name}";
        $img_url="uploads/{$img_name}";


        // Upload File
        $img->move(public_path('uploads'),$img_name);


        // Save To Database
        return Product::create([
            'name'=>$request->input('name'),
            'price'=>$request->input('price'),
            'unit'=>$request->input('unit'),
            'img_url'=>$img_url,
            'category_id'=>$request->input('category_id'),
            'user_id'=>$user_id
        ]);
    }
}
