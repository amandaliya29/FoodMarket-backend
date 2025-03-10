<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class OrderContoller extends BaseController
{

    public function list(Request $request)
    {
        try {
            // check is admin
            $user = Auth::user();
            if (!$user->is_admin) {
                return $this->sendError("Unauthorized", 401);
            }

            $query = Order::with(['products', 'receipt', 'user'])->latest();

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('status', 'LIKE', "%{$search}%");
            }

            $order = $query->get();
            return $this->sendSuccess($order, "Order retrieved successfully.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function get($id)
    {
        try {
            // check is admin
            $user = Auth::user();
            $order = Order::with(['products', 'receipt', 'user'])->find($id);

            if (!$user->is_admin && $user->id != $order->user_id) {
                return $this->sendError("Unauthorized", 401);
            }

            if (!$order) {
                return $this->sendError("Order not found", 404);
            }

            return $this->sendSuccess($order, "Order retrieved successfully.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

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

            $order = new Order;
            $order->user_id = $user->id;
            $order->house_no = (string) $request->house_no;
            $order->address = (string) $request->address;
            $order->city = (string) $request->city;
            $order->save();

            $order->products()->attach($request->product_ids);

            $receipt = new Receipt();
            $receipt->order_id = $order->id;

            $products = Product::whereIn('id', $request->product_ids)->get();
            $amount = 0;

            foreach ($request->product_ids as $id) {
                $amount += $products->firstWhere('id', $id)->price ?? 0;
            }

            $receipt->amount = $amount;
            $receipt->gst = (int) $request->gst;
            $receipt->delivery_charges = (int) $request->delivery_charges;
            $receipt->discount_applied = (int) $request->discount_applied;
            $receipt->save();
            $order->receipts_id = $receipt->id;
            $order->save();

            return $this->sendSuccess(['order_id' => $order->id], "Order created successfully.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function webhooks(Request $request)
    {
        try {
            $data = $request->all();

            $webhookSecret = Config::get('razorpay.webhooks.secret');
            $expectedSignature = hash_hmac('sha256', json_encode($data), $webhookSecret);

            if ($expectedSignature !== $request->header('X-Razorpay-Signature')) {
                Log::error("Invalid razorpay signature");
                return $this->sendError("Invalid razorpay signature", 401);
            }

            $entity = collect($data['payload']['payment']['entity']);
            $order = Order::find($entity['notes']['order_id']);
            if (!$order) {
                Log::info("Order not found", ['order_id' => $entity['notes']['order_id']]);
                return $this->sendError("Order not found", 404);
            }

            $order->payment_type = 'online';
            $valid_status = Config::get('razorpay.webhooks.valid_status');
            $order->payment_status = $entity['status'] == $valid_status ? 'success' : 'failed';
            $order->method = $entity['method'];
            $order->payment_id = $entity['id'];
            $order->card_id = $entity['card_id'];
            $order->bank = $entity['bank'];
            $order->vpa = $entity['vpa'];
            $order->upi_transaction_id = $entity['acquirer_data']['upi_transaction_id'];
            $order->status = 'confirmed';
            $order->save();

            return $this->sendSuccess([], "Order successfully confirmed.");
        } catch (\Throwable $th) {
            Log::error("Order webhooks failed." . $th->getMessage());
            return $this->sendError("Server Error", 500);
        }
    }

    public function cash(Request $request)
    {
        try {
            // validation
            $validation = Validator::make($request->all(), [
                'order_id' => 'required|numeric',
            ]);

            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            $order = Order::find($request->order_id);
            if (!$order) {
                return $this->sendError("Order not found", 404);
            }

            $order->status = 'confirmed';
            $order->save();

            return $this->sendSuccess([], "Order successfully confirmed.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function cancel(Request $request)
    {
        try {
            // validation
            $validation = Validator::make($request->all(), [
                'order_id' => 'required|numeric',
            ]);

            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            $order = Order::with('receipt')->find($request->order_id);
            if (!$order) {
                return $this->sendError("Order not found", 404);
            }

            if ($order->status == 'delivered') {
                return $this->sendError("A delivered order cannot be canceled.", 502);
            }

            if ($order->payment_type != 'cash') {
                $razorpayApi = new Api(Config::get('razorpay.key'), Config::get('razorpay.secret'));
                $payment = $razorpayApi->payment->fetch($order->payment_id);

                if ($payment['status'] === 'captured') {
                    $payment->refund(['amount' => $order->receipt->amount]);
                }
                $order->payment_status = 'refund';
            }

            $order->status = 'cancelled';
            $order->save();

            return $this->sendSuccess([], "Order successfully cancelled.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function status(Request $request)
    {
        try {
            // check is admin
            $user = Auth::user();
            if (!$user->is_admin) {
                return $this->sendError("Unauthorized", 401);
            }

            // validation
            $validation = Validator::make($request->all(), [
                'order_id' => 'required|numeric',
                'status' => 'required|string',
            ]);

            // validation error
            if ($validation->fails()) {
                return $this->sendError("Validation Error", 403);
            }

            $order = Order::find($request->order_id);
            if (!$order) {
                return $this->sendError("Order not found", 404);
            }

            if (!in_array($request->status, ['preparing', 'out_for_delivery', 'delivered'])) {
                return $this->sendError("Unable to change status to $request->status", 502);
            }

            if ($request->status == 'delivered') {
                $order->payment_status = 'success';
            }

            $order->status = $request->status;
            $order->save();

            return $this->sendSuccess([], "Order successfully changed.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function pastOrder()
    {
        try {
            $order = Order::with(['products', 'receipt', 'user'])->where('user_id', Auth::id())->whereIn('status', ['delivered', 'cancelled'])->latest()->get();
            return $this->sendSuccess($order, "Past order retrieved successfully.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }

    public function inprogressOrder()
    {
        try {
            $order = Order::with(['products', 'receipt', 'user'])->where('user_id', Auth::id())->whereNotIn('status', ['delivered', 'cancelled'])->latest()->get();
            return $this->sendSuccess($order, "In progress order retrieved successfully.");
        } catch (\Throwable $th) {
            return $this->sendError("Server Error", 500);
        }
    }
}
