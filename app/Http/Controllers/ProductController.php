<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function productPage(){
        return view('pages.dashboard.product-page');
    }
    public function customerPage(){
        return view('pages.dashboard.customer-page');
    }

}
