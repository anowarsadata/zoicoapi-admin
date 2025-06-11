<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\OrderItem;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $check_authentication = Auth::user();
        if ($check_authentication) {
            if ($check_authentication->hasRole('admin')) {
                $orders = Order::all();
                return response()->json([
                    'orders' => $orders,
                ], 200);
            } else {
                $orders = Order::find($check_authentication->id);
                return response()->json([
                    'orders' => $orders,
                ], 200);
            }
        } else {
            return response()->json([
                'message' => $check_authentication,
            ], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $check_authentication = Auth::user();
        if ($check_authentication) {
            $validatedData = $this->validate($request, [
                'user_id' => 'required|int',
                'currency_id' => 'required|int',
                'billing_address_id' => 'required|int',
                'shipping_address_id' => 'required|int',
            ]);
            if ($validatedData) {
                $result = Order::create($request->all());
                return response()->json([
                    'success' => true,
                    'message' => "Record created successfully.",
                    'order' => $result,
                ], 200);
            }
        } else {
            return response()->json([
                'message' => $check_authentication,
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $check_authentication = Auth::user();
        if ($check_authentication) {
            if ($check_authentication->hasRole('admin')) {
                $orders = Order::find($id);
                return response()->json([
                    'orders' => $orders,
                ], 200);
            }
        } else {
            return response()->json([
                'message' => $check_authentication,
            ], 200);
        }
    }


    /**
     * Display the specified resources based on user id for admin user.
     */
    public function get_orders_by_user_id(string $id)
    {
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Only allow access if the user has the 'admin' role
        if (!$user->hasRole('admin')) {
            return response()->json([
                'message' => 'Access denied. Admin role required.',
            ], 403);
        }

        // Get orders for the given user_id and join with currency data
        $orders = Order::where('user_id', $id)
            ->leftJoin('currencies', 'currencies.id', '=', 'orders.currency_id')
            ->select(
                'orders.*',
                'currencies.name as currency_name',
                'currencies.symbol as currency_symbol'
            )
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $check_authentication = Auth::user();
        if ($check_authentication && $check_authentication->hasRole('admin')) {
            $order = Order::find($id);
            if ($order) {
                if ($order->update($request->all())) {
                    return response()->json([
                        "success" => true,
                        'message' => 'Record updated.',
                        $order,
                    ], 200);
                } else {
                    return response()->json([
                        "success" => false,
                        'message' => 'No record updated!',
                        $order,
                    ], 200);
                }
            } else {
                return response()->json([
                    "success" => false,
                    'message' => 'No record found!.',
                    $order,
                ], 200);
            }

        } else {
            return response()->json([
                'message' => $check_authentication,
            ], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function cancel(Request $request, $id)
    {
        $check_authentication = Auth::user();
        if ($check_authentication) {
            $validatedData = $this->validate($request, [
                'status' => 'required|string',
                'remarks' => 'required|string',
            ]);
            $order = Order::find($id);
            if ($order) {
                if ($order->update($request->all())) {
                    return response()->json([
                        "success" => true,
                        'message' => 'Record updated.',
                        $order,
                    ], 200);
                } else {
                    return response()->json([
                        "success" => false,
                        'message' => 'No record updated!',
                        $order,
                    ], 200);
                }
            } else {
                return response()->json([
                    "success" => false,
                    'message' => 'No record found!.',
                    $order,
                ], 200);
            }

        } else {
            return response()->json([
                'message' => $check_authentication,
            ], 200);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $check_authentication = Auth::user();
        if ($check_authentication && $check_authentication->hasRole('admin')) {
            $product = Order::find($id);
            if (!$product) {
                return response()->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
            }
            $product->delete();
            return response()->json(['message' => 'Order deleted'], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => $check_authentication,
            ], 200);
        }
    }

    public function createOrderFromCart(Request $request)
    {
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $cart = Cart::where('user_id', $user->id)->first();

    if (!$cart) {
        return response()->json(['message' => 'Cart not found.'], 404);
    }

    $cartItems = CartItem::where('cart_id', $cart->id)->with('product')->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'Cart is empty.'], 400);
    }

    $validated = $request->validate([
        'currency_id' => 'required|int',
        'billing_address_id' => 'required|int',
        'shipping_address_id' => 'required|int',
        'remarks' => 'nullable|string',
        'payment_method' => 'nullable|string',
    ]);

    $subtotal = 0;

    foreach ($cartItems as $item) {
        $price = $item->product->price_uk ?? 0;
        $discount = $item->product->discount ?? 0;
        //$finalPrice = max($price - $discount, 0);
        $finalPrice = $price;
        $subtotal += $finalPrice * $item->quantity;
    }

    // Convert subtotal to string (2 decimal points)
    $subtotalFormatted = number_format($subtotal, 2, '.', '');

    $order = Order::create([
        'user_id' => $user->id,
        'currency_id' => $validated['currency_id'],
        'billing_address_id' => $validated['billing_address_id'],
        'shipping_address_id' => $validated['shipping_address_id'],
        'remarks' => $validated['remarks'] ?? null,
        'payment_method' => $validated['payment_method'] ?? null,
        'subtotal' => $subtotalFormatted,
        'total' => $subtotalFormatted, // same as subtotal unless you want to add shipping/taxes
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    foreach ($cartItems as $item) {
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'price' => $item->product->price_uk ?? 0,
            'discount' => $item->product->discount ?? 0,
        ]);
    }

    // Optional: clear the cart
    CartItem::where('cart_id', $cart->id)->delete();
    $cart->delete();

    return response()->json([
        'success' => true,
        'message' => 'Order created successfully from cart.',
        'order' => $order,
    ], 201);
    }

}
