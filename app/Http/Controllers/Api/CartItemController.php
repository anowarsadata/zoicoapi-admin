<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CartItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = CartItem::all();

        return response()->json([
            'success' => true,
            'items' => $items,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not used in API context
        return response()->json(['message' => 'Not supported.'], 405);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $cartItem = CartItem::where('cart_id', $validated['cart_id'])
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            // If exists, increment quantity
            $cartItem->quantity += 1;
            $cartItem->save();

            return response()->json([
                'success' => true,
                'message' => 'Cart item quantity increased.',
                'cart_item' => $cartItem,
            ], 200);
        }

        // Create new cart item with quantity = 1
        $cartItem = CartItem::create([
            'cart_id' => $validated['cart_id'],
            'product_id' => $validated['product_id'],
            'quantity' => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart item created successfully.',
            'cart_item' => $cartItem,
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cartItem = CartItem::find($id);

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'cart_item' => $cartItem,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not used in API context
        return response()->json(['message' => 'Not supported.'], 405);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cartItem = CartItem::find($id);

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.',
            ], 404);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully.',
            'cart_item' => $cartItem,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cartItem = CartItem::find($id);

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found.',
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart item deleted successfully.',
        ], 200);
    }
}
