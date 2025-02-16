<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrderContoller extends BaseController
{

    public function create(Request $request)
    {
        try {
            // validation
            $validation = Validator::make($request->all(), [
                'product_ids' => 'required|array',
                'gst' => 'nullable|numeric',
                'delivery_charges' => 'nullable|numeric',
                'discount_applied' => 'nullable|numeric',
            ]);
            
            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            $user = Auth::user();
            $order = Order::create(['user_id' => $user->id]);

            $order->products()->attach($request->product_ids);
            
            $receipt = new Receipt(); 
            $receipt->order_id = $order->id; 
            $amount = Product::whereIn('id', $request->product_ids)->sum('price');
            $receipt->amount = $amount; 
            $receipt->gst = (int) $request->gst; 
            $receipt->delivery_charges = (int) $request->delivery_charges; 
            $receipt->discount_applied = (int) $request->discount_applied; 
            $receipt->save();

            return $this->sendSuccess(['order_id' => $order->id], "Order created successfully.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }
}
