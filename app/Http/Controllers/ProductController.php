<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;//for delete File image

class ProductController extends Controller
{
    public function productPage(): View
    {
        return view('pages.dashboard.product-page');
    }
    public function listProducts()
    {
        try {
            $user_id = Auth::id();
            $rows = Product::where('user_id', $user_id)->get();
            return response()->json(['status' => 'success', 'rows' => $rows]);
        } catch (Exception $e) {
            return response()->json(
                [
                    'status' => "Fail",
                    'message' => $e->getMessage(),

                ]
            );
        }
    }
    public function createProduct(Request $request)
    {
        try {
            $user_id = Auth::id();

            $request->validate([
                'name' => 'required|string|max:50',
                'price' => 'required|string|max:50',
                'unit' => 'required|string|max:11',
                'img' => 'required|image|max:6048',
                "category_id"=> 'required|string',
            ]);
            // Prepare File Name & Path
            $img = $request->file('img');
            $t = time();
            $file_name = $img->getClientOriginalName();
            $img_name = "{$user_id}-{$t}-{$file_name}";
            $img_url = "uploads/{$img_name}";

            // Upload File
            $img->move(public_path('uploads'), $img_name);

            // Save To Database
            Product::create([
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'unit' => $request->input('unit'),
                'img_url' => $img_url,
                'category_id' => $request->input('category_id'),
                'user_id' => $user_id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully',
                
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => "fail",
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function productDelete(Request $request)
    {
        $user_id= Auth::id();
        $product_id=$request->input('id');
        $filePath=$request->input('file_path');
        File::delete($filePath);
        return Product::where('id',$product_id)->where('user_id',$user_id)->delete();
    }
}
