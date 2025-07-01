<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\CartItemTopping;
use App\Models\Table;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\OrderItem;
use App\Models\OrderItemTopping;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{
    //
    public function index(Request $request)
    {
        $table_id = session('table_id');
        if (!$table_id) {
            return redirect()->back()->with('error', 'Không xác định được bàn.');
        }

        // Lấy danh sách sản phẩm trong giỏ (CartItem), kèm thông tin product, size và toppings
        $cartItems = CartItem::with(['product', 'size', 'toppings.topping'])
            ->where('table_id', $table_id)
            ->get();

        return view('User.profile.shoppingcart', [
            'cartItems' => $cartItems,
        ]);
    }
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'size_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:5',
            'note' => 'nullable|string|max:150',
            'topping_quantities' => 'nullable|array',
        ]);

        $table_id = session('table_id');
        if (!$table_id) {
            return response()->json(['success' => false, 'message' => 'Không xác định được bàn.'], 400);
        }

        DB::beginTransaction();
        try {
            $cartItem = CartItem::create([
                'table_id' => $table_id,
                'product_id' => $validated['product_id'],
                'size_id' => $validated['size_id'],
                'quantity' => $validated['quantity'],
                'note' => $validated['note'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($validated['topping_quantities'])) {
                foreach ($validated['topping_quantities'] as $toppingId => $qty) {
                    if ((int) $qty > 0) {
                        // Lấy giá topping từ database
                        $toppingModel = \App\Models\Topping::find($toppingId);
                        $toppingPrice = $toppingModel ? $toppingModel->price : 0;
                        CartItemTopping::create([
                            'cart_item_id' => $cartItem->id,
                            'topping_id' => $toppingId,
                            'quantity' => $qty,
                            'price' => $toppingPrice,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng thành công!',
                'cart' => [
                    'totalQuantity' => $this->getCartQuantityByTable($table_id),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Add to cart failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thêm vào giỏ hàng!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function getCartQuantityByTable($tableId)
    {
        return CartItem::where('table_id', $tableId)->sum('quantity');
    }

    public function deleteItemCart(Request $request, int $cart_item_id)
    {
        try {
            $table_id = session('table_id');
            if (!$table_id) {
                return response()->json(['success' => false, 'message' => 'Không xác định được bàn.'], 400);
            }

            $cartItem = CartItem::where('id', $cart_item_id)
                ->where('table_id', $table_id)
                ->first();

            if (!$cartItem) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.'], 404);
            }

            // Xóa toppings trước (nếu có)
            CartItemTopping::where('cart_item_id', $cartItem->id)->delete();

            // Xóa cart item
            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
                'cart' => [
                    'totalQuantity' => $this->getCartQuantityByTable($table_id),
                    'totalPrice' => $this->getTotalPriceByTable($table_id),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Xóa sản phẩm thất bại: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa sản phẩm khỏi giỏ hàng!',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function deleteAllItem(Request $request)
    {
        try {
            $table_id = session('table_id');
            if (!$table_id) {
                return response()->json(['success' => false, 'message' => 'Không xác định được bàn.'], 400);
            }

            $cartItemIds = CartItem::where('table_id', $table_id)->pluck('id');

            // Xóa topping trước
            CartItemTopping::whereIn('cart_item_id', $cartItemIds)->delete();

            // Xóa các item trong giỏ
            CartItem::where('table_id', $table_id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa toàn bộ sản phẩm trong giỏ hàng.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Xóa toàn bộ giỏ hàng thất bại: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa giỏ hàng!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function minusOnQuantity(Request $request, int $cart_item_id)
    {
        try {
            $table_id = session('table_id');
            if (!$table_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không xác định được bàn.'
                ], 400);
            }

            $cartItem = CartItem::where('id', $cart_item_id)
                ->where('table_id', $table_id)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.'
                ], 404);
            }

            // Nếu đã là 1 thì không giảm nữa
            if ($cartItem->quantity <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng tối thiểu là 1.'
                ]);
            }

            $cartItem->quantity -= 1;
            $cartItem->updated_at = now();
            $cartItem->save();

            return response()->json([
                'success' => true,
                'message' => 'Đã giảm số lượng sản phẩm.',
                'item' => [
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price + ($cartItem->size->price ?? 0),
                    'topping_total' => $cartItem->toppings->reduce(function ($carry, $t) {
                        return $carry + ($t->topping->price * $t->quantity);
                    }, 0),
                ],
                'cart' => [
                    'totalQuantity' => $this->getCartQuantityByTable($table_id),
                    'totalPrice' => $this->getTotalPriceByTable($table_id),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Giảm số lượng thất bại: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi giảm số lượng!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function increaseOnQuantity(Request $request, int $cart_item_id)
    {
        try {
            $table_id = session('table_id');
            if (!$table_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không xác định được bàn.'
                ], 400);
            }

            $cartItem = CartItem::where('id', $cart_item_id)
                ->where('table_id', $table_id)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.'
                ], 404);
            }

            // Giới hạn tối đa là 5
            if ($cartItem->quantity >= 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng tối đa cho mỗi món là 5.'
                ]);
            }

            $cartItem->quantity += 1;
            $cartItem->updated_at = now();
            $cartItem->save();

            return response()->json([
                'success' => true,
                'message' => 'Đã tăng số lượng sản phẩm.',
                'item' => [
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price + ($cartItem->size->price ?? 0),
                    'topping_total' => $cartItem->toppings->reduce(function ($carry, $t) {
                        return $carry + ($t->topping->price * $t->quantity);
                    }, 0),
                ],
                'cart' => [
                    'totalQuantity' => $this->getCartQuantityByTable($table_id),
                    'totalPrice' => $this->getTotalPriceByTable($table_id),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Tăng số lượng thất bại: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tăng số lượng sản phẩm!',
                'error' => $e->getMessage(),
            ]);
        }
    }
    private function getTotalPriceByTable($tableId)
    {
        $items = CartItem::with(['product', 'size', 'toppings.topping'])
            ->where('table_id', $tableId)->get();

        $total = 0;
        foreach ($items as $item) {
            $sizePrice = $item->size ? $item->size->price : 0;
            $productPrice = $item->product->price + $sizePrice;

            $toppingTotal = $item->toppings->reduce(function ($carry, $t) {
                return $carry + ($t->topping->price * $t->quantity);
            }, 0);

            $total += ($productPrice + $toppingTotal) * $item->quantity;
        }

        return $total;
    }

public function submitCart(Request $request)
{
    $table_id = session('table_id');
    if (!$table_id) {
        return redirect()->back()->with('error', 'Không xác định được bàn.');
    }

    $cartItems = CartItem::with(['product', 'size', 'toppings.topping'])
        ->where('table_id', $table_id)
        ->get();

    if ($cartItems->isEmpty()) {
        return redirect()->back()->with('error', 'Giỏ hàng trống.');
    }

    DB::beginTransaction();
    try {
        $total_price = 0;

        foreach ($cartItems as $item) {
            $sizePrice = $item->size ? $item->size->price : 0;
            $productPrice = ($item->product->price ?? 0) + $sizePrice;

            $toppingTotal = $item->toppings->reduce(function ($carry, $t) {
                return $carry + (($t->topping->price ?? 0) * $t->quantity);
            }, 0);

            $total_price += ($productPrice + $toppingTotal) * $item->quantity;
        }

        $order = Order::create([
            'order_code' => 'ORD' . now()->timestamp,
            'table_id' => $table_id,
            'total_price' => round($total_price, 2),
            'payment_method_id' => 1,
            'order_status_id' => 0, // trạng thái "chờ xác nhận"
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($cartItems as $item) {
            $sizePrice = $item->size ? $item->size->price : 0;
            $productPrice = ($item->product->price ?? 0) + $sizePrice;

            $toppingTotal = $item->toppings->reduce(function ($carry, $t) {
                return $carry + (($t->topping->price ?? 0) * $t->quantity);
            }, 0);

            $totalPricePerItem = round(($productPrice + $toppingTotal) * $item->quantity, 2);

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'size_id' => $item->size_id,
                'quantity' => $item->quantity,
                'note' => $item->note ?? '',
                'total_price' => $totalPricePerItem,
                'created_at' => now(),
            ]);

            foreach ($item->toppings as $t) {
                OrderItemTopping::create([
                    'order_item_id' => $orderItem->id,
                    'topping_id' => $t->topping_id,
                    'quantity' => $t->quantity,
                    'price' => $t->price,
                ]);
            }
        }

        // Xóa giỏ hàng sau khi đặt hàng thành công
        CartItemTopping::whereIn('cart_item_id', $cartItems->pluck('id'))->delete();
        CartItem::where('table_id', $table_id)->delete();

        DB::commit();
        return redirect()->route('user.payment')->with('success', 'Gửi đơn hàng thành công!');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Gửi giỏ hàng thất bại: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Lỗi khi gửi đơn hàng.');
    }
}

}
