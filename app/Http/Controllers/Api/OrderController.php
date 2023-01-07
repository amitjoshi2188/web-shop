<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;
use Validator;

class OrderController extends Controller
{
    /**
     * Display orders listing.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $orders = Order::all();
        return response()->json([
            "success" => true,
            "message" => "Order List.",
            "data" => $orders
        ], HTTPResponse::HTTP_OK);
    }

    /**
     * Store a newly created order.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'customer_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()],
                HTTPResponse::HTTP_UNAUTHORIZED);
        }

        $product = Product::find($input['product_id']);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => "Product not exists."],
                HTTPResponse::HTTP_NOT_FOUND);
        }

        //creating order
        $order = Order::create([
            'customer_id' => $input['customer_id']
        ]);

        //creating order item
        $orderItem = Orderitem::create([
            'product_id' => $input['product_id'],
            'order_id' => $order->id,
            'quantity' => $input['quantity'],
            'price' => $product->price,
            'total' => ($product->price * $input['quantity']),
        ]);

        if ($orderItem->id) {
            return response()->json([
                "success" => true,
                "message" => "Order created successfully.",
            ], HTTPResponse::HTTP_OK);
        } else {
            return response()->json(['status' => 'error', 'message' => "Order not created, please try again letter."],
                HTTPResponse::HTTP_BAD_REQUEST);

        }

    }

    /**
     * Display the specified order information.
     * @param int|null $orderId
     * @return JsonResponse
     */
    public function show(int $orderId = null): JsonResponse
    {
        $order = Order::find($orderId);

        if (is_null($order)) {
            return response()->json(['status' => 'error', 'message' => "Order not not found."],
                HTTPResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            "success" => true,
            "message" => "Order retrieved successfully.",
            "data" => $order
        ], HTTPResponse::HTTP_OK);
    }

    /**
     * Update the specified order with product details.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'order_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()],
                HTTPResponse::HTTP_UNAUTHORIZED);
        }

        $order = Order::find($input['order_id']);

        if (is_null($order)) {
            return response()->json(['status' => 'error', 'message' => "Order not not found."],
                HTTPResponse::HTTP_NOT_FOUND);
        }

        if ($order->payed > 0) {
            return response()->json(['status' => 'error',
                'message' => "Product cant be added as order amount has been paid."],
                HTTPResponse::HTTP_UNAUTHORIZED);
        }

        $product = Product::find($input['product_id']);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => "Product not exists."],
                HTTPResponse::HTTP_NOT_FOUND);
        }

        //creating order item
        $orderItem = Orderitem::create([
            'product_id' => $input['product_id'],
            'order_id' => $input['order_id'],
            'quantity' => $input['quantity'],
            'price' => $product->price,
            'total' => ($product->price * $input['quantity']),
        ]);

        if ($orderItem->id) {
            return response()->json([
                "success" => true,
                "message" => "Product added in order successfully.",
            ], HTTPResponse::HTTP_OK);
        } else {
            return response()->json(['status' => 'error',
                'message' => "Order not created, please try again letter."],
                HTTPResponse::HTTP_BAD_REQUEST);
        }

    }

    /**
     * Remove the specified order from db.
     * @param $orderId
     * @return JsonResponse
     */
    public function destroy($orderId): JsonResponse
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], HTTPResponse::HTTP_NOT_FOUND);
        }

        if ($order->delete()) {
            return response()->json([
                'status' => 'success',
                'message' => "Order has been removed successfully."
            ], HTTPResponse::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Order could not be deleted.'
            ], HTTPResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Calculate total cart value to be paid.
     * @param int|null $orderId
     * @return JsonResponse
     */
    public function calculateTotalCartValue(int $orderId = null): JsonResponse
    {
        $order = Order::find($orderId);

        if (is_null($order)) {
            return response()->json(['status' => 'error', 'message' => "Order not found."],
                HTTPResponse::HTTP_NOT_FOUND);
        }
        $totalCartValue = $order->orderItems()->sum('total');

        return response()->json([
            "success" => true,
            "message" => "Total Cart value to be paid.",
            "data" => $totalCartValue
        ], HTTPResponse::HTTP_OK);
    }

    /**
     * Pay order amount using request parameters.
     * @param Request $request
     * @return JsonResponse
     */
    public function payOrder(Request $request): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'customer_id' => 'required',
            'order_id' => 'required',
            'payed' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()],
                HTTPResponse::HTTP_UNAUTHORIZED);
        }
        $customer = Customer::find($input['customer_id']);

        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => "Customer not exists."],
                HTTPResponse::HTTP_NOT_FOUND);
        }

        $order = Order::find($input['order_id']);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found.'
            ], HTTPResponse::HTTP_NOT_FOUND);
        }

        if ($order->payed > 0) {
            return response()->json(['status' => 'error', 'message' => "Order amount already paid."],
                HTTPResponse::HTTP_UNAUTHORIZED);
        }

        $totalCartValue = $order->orderItems()->sum('total');
        if ($input['payed'] !== $totalCartValue) {
            return response()->json([
                'status' => 'error',
                'message' => "Order amount $totalCartValue is not same as entered amount $request->payed"
            ], HTTPResponse::HTTP_UNAUTHORIZED);
        } else {
            //update order payment.
            $order->payed = $totalCartValue;
            $order->save();

            return response()->json([
                'status' => 'success',
                'message' => "Payment Successful."
            ], HTTPResponse::HTTP_OK);
        }
    }
}
