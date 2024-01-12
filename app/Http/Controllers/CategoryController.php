<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    //
    public function categoryPage(){
        return view('pages.dashboard.category-page');
    }

    function CategoryList(Request $request){
        try{
            $user_id=Auth::id();
            $rows= Category::where('user_id',$user_id)->get();
            return response()->json(['status' => 'success', 'rows' => $rows]);
        }catch (Exception $e){
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }

    public function createCategories(Request $request){
        try{
            $request->validate([
                'name' => 'required|string|min:2'
            ]);
            $user_id=Auth::id();
            Category::create([
                'name'=>$request->input('name'),
                'user_id'=>$user_id
            ]);
            return response()->json(['status' => 'success', 'message' => "Request Successful"]);
        }catch (Exception $e){
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }
}
