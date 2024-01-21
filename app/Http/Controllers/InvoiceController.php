<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function invoicePage():View{
        return view('pages.dashboard.invoice-page');
    }
    public function listInvoice(Request $request){
        try{
            $user_id = Auth::id();
            $rows = Invoice::where('user_id',$user_id)
                        ->with('customer')->get();
            return response()->json(['status' => 'success', 'rows' => $rows]);

        }
        catch(Exception $e)
        {
            return response()->json([
                'status'=>'fail',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function createInvoice(Request $request){
        DB::beginTransaction();

        try{
            $request->validate([
                'total' => 'required|string|max:50',
                'discount' => 'required|string|max:50',
                'vat' => 'required|string|max:50',
                'payable' => 'required|string|max:50',
                'customer_id' => 'required|string|max:50',
            ]);
            $user_id = Auth::id();
            $total = $request->input('total');
            $discount = $request->input('discount');
            $vat = $request->input('vat');
            $payable = $request->input('payable');
            $customer_id = $request->input('customer_id');

            $invoice = Invoice::create([
                'total' => $total,
                'discount' => $discount,
                'vat' => $vat,
                'payable' => $payable,
                'user_id' => $user_id,
                'customer_id' => $customer_id,
            ]);

            $invoiceId = $invoice->id;

            $products = $request->input('products');
            foreach ($products as $item) {
                InvoiceProduct::create([
                    'invoice_id'=>$invoiceId,
                    'user_id'=>$user_id,
                    'product_id' => $item['product_id'], 
                    'qty'=>$item['qty'], 
                    'sale_price'=>$item['sale_price']
                ]);
            }

            DB::commit();
            return response()->json([
                'status'=>'success',
                'message'=>'Invoice created success'
            ]);
        }
        catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }
    public function invoiceDetails(Request $request){
        try{

            $user_id = Auth::id();

            $customerDetails = Customer::where('user_id',$user_id)
                                    ->where('id',$request->input('cus_id'))
                                    ->first();

            $invoiceTotal=Invoice::where('user_id','=',$user_id)
                                    ->where('id',$request->input('inv_id'))
                                    ->first();
            
            $invoiceProduct=InvoiceProduct::where('invoice_id',$request->input('inv_id'))
                                    ->where('user_id',$user_id)->with('product')
                                    ->get();
            $rows = array(
                'customer'=>$customerDetails,
                'invoice'=>$invoiceTotal,
                'product'=>$invoiceProduct

            );
            return response()->json(['status' => 'success', 'rows' => $rows]);
            
        }
        catch(Exception $e){
            return response()->json([
                'status'=>'fail',
                'message'=>$e->getMessage()
            ]);
        }
    }
    public function invoiceDelete(Request $request){
        DB::beginTransaction();

        try {
            $user_id = Auth::id();

            InvoiceProduct::where('invoice_id',$request->input('invoice_id'))
                                    ->where('user_id',$user_id)
                                    ->delete();
            
            Invoice::where('id',$request->input('invoice_id'))->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Request Successful"]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }
    
}
