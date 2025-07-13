<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\CartItemTopping;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemTopping;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{
    public function index(Request $request)
    {
        $table_id = session('table_id');

        // 1. Nếu không có session table_id => không xác định được bàn
        if (!$table_id || !is_numeric($table_id)) {
            return redirect('/404')->with('error', 'Không xác định được bàn hoặc bàn không hợp lệ.');
        }

        // 2. Kiểm tra bàn có tồn tại không
        $table = Table::find($table_id);
        if (!$table) {
            session()->forget(['table_id', 'qr_token']);
            Cookie::queue(Cookie::forget('table_id'));
            return redirect('/404')->with('error', 'Bàn không tồn tại.');
        }

        // 3. Kiểm tra trạng thái bàn (chỉ cho bàn đang phục vụ: table_status_id == 2)
        if ($table->table_status_id != 2) {
            // Xoá giỏ hàng gắn với bàn
            CartItem::where('table_id', $table->id)->delete();

            // Xoá session và cookie liên quan đến bàn
            session()->forget(['table_id', 'qr_token']);
            Cookie::queue(Cookie::forget('table_id'));

            if ($table->table_status_id == 1) {
                return redirect('/404')->with('error', 'Bàn chưa được kích hoạt.');
            } elseif ($table->table_status_id == 3) {
                return redirect('/404')->with('error', 'Bàn đang được dọn dẹp.');
            } else {
                return redirect('/404')->with('error', 'Bàn không hợp lệ.');
            }
        }

        // 4. Lấy danh sách sản phẩm trong giỏ hàng theo bàn
        $cartItems = CartItem::with(['product', 'size', 'toppings.topping'])
            ->where('table_id', $table_id)
            ->paginate(4); // phân trang mỗi trang 4 món

        $allCartItems = CartItem::with(['product', 'size', 'toppings.topping'])
            ->where('table_id', $table_id)
            ->get();

        // 5. Trả về view giỏ hàng
        return view('User.profile.shoppingcart', [
            'cartItems' => $cartItems,
            'table_id' => $table_id,
            'allCartItems' => $allCartItems,
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'size_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:20',
            'note' => 'nullable|string|max:150',
            'topping_quantities' => 'nullable|array',
        ]);

        $table_id = session('table_id');

        DB::beginTransaction();
        try {
            $query = CartItem::where('table_id', $table_id)
                ->where('product_id', $validated['product_id'])
                ->where('size_id', $validated['size_id'])
                ->where('note', $validated['note']);

            $existingItem = $query->first();
            $sameTopping = true;

            if ($existingItem) {
                $existingToppings = collect($existingItem->toppings()->get())->pluck('quantity', 'topping_id')->map(fn($q) => (int) $q)->toArray();
                $newToppings = collect($validated['topping_quantities'] ?? [])->map(fn($q) => (int) $q)->toArray();

                if ($existingToppings != $newToppings) {
                    $sameTopping = false;
                }
            }

            if ($existingItem && $sameTopping) {
                $existingItem->quantity += $validated['quantity'];
                $existingItem->save();
            } else {
                $existingItem = CartItem::create([
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
                            $topping = \App\Models\Topping::find($toppingId);
                            $toppingPrice = $topping ? $topping->price : 0;

                            CartItemTopping::create([
                                'cart_item_id' => $existingItem->id,
                                'topping_id' => $toppingId,
                                'quantity' => $qty,
                                'price' => $toppingPrice,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            $totalQuantity = CartItem::where('table_id', $table_id)->sum('quantity');
            // Cập nhật lại session giỏ hàng
            session([
                'cart' => (object) [
                    'totalQuantity' => $totalQuantity
                ]
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng thành công!',
                'cart' => [
                    'totalQuantity' => $totalQuantity
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Lỗi thêm giỏ hàng: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không xác định được bàn! Quét mã QR để sử dụng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'size_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:5',
            'note' => 'nullable|string|max:150',
            'topping_quantities' => 'nullable|array',
            'table_id' => 'required|integer',
        ]);

        $table_id = $validated['table_id'];
        if (!$table_id) {
            return response()->json(['success' => false, 'message' => 'Không xác định được bàn.'], 400);
        }

        DB::beginTransaction();
        try {
            // Lấy danh sách topping để so sánh
            $newToppings = $validated['topping_quantities'] ?? [];

            // Tìm xem đã có sản phẩm giống trong giỏ chưa
            $existingItems = CartItem::where([
                'table_id' => $table_id,
                'product_id' => $validated['product_id'],
                'size_id' => $validated['size_id'],
                'note' => $validated['note'] ?? null,
            ])->get();

            foreach ($existingItems as $item) {
                $existingToppings = $item->toppings->pluck('quantity', 'topping_id')->toArray();

                if ($existingToppings == $newToppings) {
                    // Nếu trùng topping → tăng số lượng
                    $item->quantity += $validated['quantity'];
                    $item->updated_at = now();
                    $item->save();

                    DB::commit();
                    $totalQuantity = $this->getCartQuantityByTable($table_id);
                    session([
                        'cart' => (object) [
                            'totalQuantity' => $totalQuantity
                        ]
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Đã cập nhật giỏ hàng.',
                        'cart' => ['totalQuantity' => $totalQuantity]
                    ]);
                }
            }

            // Nếu không trùng → tạo mới
            $cartItem = CartItem::create([
                'table_id' => $table_id,
                'product_id' => $validated['product_id'],
                'size_id' => $validated['size_id'],
                'quantity' => $validated['quantity'],
                'note' => $validated['note'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($newToppings)) {
                foreach ($newToppings as $toppingId => $qty) {
                    if ((int) $qty > 0) {
                        $topping = \App\Models\Topping::find($toppingId);
                        $price = $topping ? $topping->price : 0;

                        CartItemTopping::create([
                            'cart_item_id' => $cartItem->id,
                            'topping_id' => $toppingId,
                            'quantity' => $qty,
                            'price' => $price,
                        ]);
                    }
                }
            }

            DB::commit();
            $totalQuantity = $this->getCartQuantityByTable($table_id);
            session([
                'cart' => (object) [
                    'totalQuantity' => $totalQuantity
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng thành công!',
                'cart' => ['totalQuantity' => $totalQuantity]
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

    private function getCartQuantityByTable($table_id)
    {
        return CartItem::where('table_id', $table_id)->sum('quantity');
    }

    public function deleteItemCart(Request $request, int $cart_item_id)
{
    try {
        $table_id = session('table_id');
        if (!$table_id) {
            return response()->json([
                'success' => false,
                'message' => 'Không xác định được bàn.'
            ], 400);
        }

        // Tìm cart item theo id + table
        $cartItem = CartItem::where('id', $cart_item_id)
            ->where('table_id', $table_id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ hàng.'
            ], 404);
        }

        // Xóa toppings nếu có
        CartItemTopping::where('cart_item_id', $cartItem->id)->delete();

        // Xóa cart icon session nếu cần
        session()->forget('cart');

        // Xóa cart item
        $cartItem->delete();

        // Tính lại số lượng sản phẩm còn lại
        $totalItems = CartItem::where('table_id', $table_id)->count();
        $perPage = 4; // ⚠ Dũng đang dùng paginate(4)
        $currentPage = max((int)$request->input('page', 1), 1);
        $lastPage = max(ceil($totalItems / $perPage), 1);

        // Nếu sau khi xóa, currentPage > lastPage ⇒ quay về lastPage
        $newPage = $currentPage > $lastPage ? $lastPage : null;

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            'cart' => [
                'totalQuantity' => $this->getCartQuantityByTable($table_id),
                'totalPrice' => $this->getTotalPriceByTable($table_id),
            ],
            'page' => $newPage // nếu khác currentPage thì frontend sẽ reload
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

            session()->forget('cart'); //xóa session icon cart

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

            // Giới hạn tối đa là 20
            if ($cartItem->quantity >= 20) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng tối đa cho mỗi món là 20.'
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

    // public function submitCart(Request $request)
    // {
    //     $table_id = session('table_id');
    //     if (!$table_id) {
    //         return redirect()->back()->with('error', 'Không xác định được bàn.');
    //     }

    //     $cartItems = CartItem::with(['product', 'size', 'toppings.topping'])
    //         ->where('table_id', $table_id)
    //         ->get();

    //     if ($cartItems->isEmpty()) {
    //         return redirect()->back()->with('error', 'Giỏ hàng trống.');
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $total_price = 0;

    //         foreach ($cartItems as $item) {
    //             $sizePrice = $item->size ? $item->size->price : 0;
    //             $productPrice = ($item->product->price ?? 0) + $sizePrice;

    //             $toppingTotal = $item->toppings->reduce(function ($carry, $t) {
    //                 return $carry + (($t->topping->price ?? 0) * $t->quantity);
    //             }, 0);

    //             $total_price += ($productPrice + $toppingTotal) * $item->quantity;
    //         }

    //         $order = Order::create([
    //             'order_code' => 'ORD' . now()->timestamp,
    //             'table_id' => $table_id,
    //             'total_price' => round($total_price, 2),
    //             'payment_method_id' => 1,
    //             'order_status_id' => 0, // trạng thái "chờ xác nhận"
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         foreach ($cartItems as $item) {
    //             $sizePrice = $item->size ? $item->size->price : 0;
    //             $productPrice = ($item->product->price ?? 0) + $sizePrice;

    //             $toppingTotal = $item->toppings->reduce(function ($carry, $t) {
    //                 return $carry + (($t->topping->price ?? 0) * $t->quantity);
    //             }, 0);

    //             $totalPricePerItem = round(($productPrice + $toppingTotal) * $item->quantity, 2);

    //             $orderItem = OrderItem::create([
    //                 'order_id' => $order->id,
    //                 'product_id' => $item->product_id,
    //                 'size_id' => $item->size_id,
    //                 'quantity' => $item->quantity,
    //                 'note' => $item->note ?? '',
    //                 'total_price' => $totalPricePerItem,
    //                 'created_at' => now(),
    //             ]);

    //             foreach ($item->toppings as $t) {
    //                 OrderItemTopping::create([
    //                     'order_item_id' => $orderItem->id,
    //                     'topping_id' => $t->topping_id,
    //                     'quantity' => $t->quantity,
    //                     'price' => $t->price,
    //                 ]);
    //             }
    //         }

    //         \Log::info('Phương thức gửi:', ['method' => $request->method()]);

    //         // Xóa giỏ hàng sau khi đặt hàng thành công
    //         CartItemTopping::whereIn('cart_item_id', $cartItems->pluck('id'))->delete();
    //         CartItem::where('table_id', $table_id)->delete();
    //         session()->forget('cart');
    //         DB::commit();

    //         if ($order->table_id == session('table_id')) {
    //             session(['current_order_code' => $order->order_code]);
    //         }
    //         return redirect()->route('user.payment', ['order_code' => $order->order_code])->with('message', 'Đặt hàng thành công!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         \Log::error('Gửi giỏ hàng thất bại: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Lỗi khi gửi đơn hàng.');
    //     }
    // }
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
            // Tính tổng tiền giỏ hàng
            $total_price = 0;
            foreach ($cartItems as $item) {
                $sizePrice = $item->size ? $item->size->price : 0;
                $productPrice = ($item->product->price ?? 0) + $sizePrice;

                $toppingTotal = $item->toppings->reduce(function ($carry, $t) {
                    return $carry + (($t->topping->price ?? 0) * $t->quantity);
                }, 0);

                $total_price += ($productPrice + $toppingTotal) * $item->quantity;
            }

            // Tìm đơn hàng đang ở trạng thái "Xác nhận" (status = 0) để gộp
            $existingOrder = Order::where('table_id', $table_id)
                ->where('order_status_id', 0)       // Xác nhận
                ->where('order_status_id', '!=', 5) // Không bị hủy
                ->latest()
                ->first();

            // Nếu có đơn phù hợp → gộp, nếu không → tạo đơn mới
            if ($existingOrder) {
                $order = $existingOrder;
                // Cộng dồn giá trị đơn hàng
                $order->total_price += round($total_price, 2);
                $order->updated_at = now();
                $order->save();
            } else {
                $order = Order::create([
                    'order_code' => 'ORD' . now()->timestamp,
                    'table_id' => $table_id,
                    'total_price' => round($total_price, 2),
                    'payment_method_id' => null, // Sẽ cập nhật sau
                    'order_status_id' => 0, // trạng thái "Xác nhận"
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Thêm từng món vào bảng order_items
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

                // Gắn topping cho từng món
                foreach ($item->toppings as $t) {
                    OrderItemTopping::create([
                        'order_item_id' => $orderItem->id,
                        'topping_id' => $t->topping_id,
                        'quantity' => $t->quantity,
                        'price' => $t->price,
                    ]);
                }
            }

            // Dọn giỏ hàng
            CartItemTopping::whereIn('cart_item_id', $cartItems->pluck('id'))->delete();
            CartItem::where('table_id', $table_id)->delete();
            session()->forget('cart');

            // Lưu mã đơn hàng hiện tại
            session(['current_order_code' => $order->order_code]);

            DB::commit();
            return redirect()->route('user.payment', ['order_code' => $order->order_code])
                ->with('message', 'Đặt hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gửi giỏ hàng thất bại: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi gửi đơn hàng.');
        }
    }
}
