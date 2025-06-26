<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    //
    public function index(Request $request)
    {
        if (session('buy-now') != null) {
            $request->session()->forget('buy-now');
        }
        $cart = session('cart') ? session('cart') : null;

        if ($cart != null) {
            //kiểm tra lại các sp trong giỏ, nếu sp có trong giỏ so với db có status là 0 thì xóa sp khỏi giỏ
            foreach ($cart->listProductVariants as $item) {
                $var = ProductVariant::find($item['variant_info']->id);
                //kiểm tra nếu variant bị ẩn hoặc  = 0 hoặc bị xóa vĩnh viễn thì giỏ hàng cũng phải xóa mât
                if ($var->status == 0 || $var == null || $var->stock == 0) {
                    $cart->deleteItemCart($var->id != null ? $var->id : $item['variant_info']->id);
                    if ($cart->totalQuantity == 0)
                        $request->session()->forget('cart');
                    else
                        $request->session('cart')->put('cart', $cart);
                } else {
                    //nếu số lượng thay đổi thì số lượng trong giỏ hàng cũng thay đổi
                    if ($var->stock != $item['variant_info']->stock) {
                        $cart->deleteItemCart($var->id);
                        $cart->addToCart($var->product, $var, $var->stock, $var->id);
                        $request->session('cart')->put('cart', $cart);
                    }
                    //nếu giá thay đổi thì giá trong giỏ hàng cũng phải thay đổi
                    if ($var->price != $item['variant_info']->price) {
                        $cart->deleteItemCart($var->id);
                        $cart->addToCart($var->product, $var, $item['quantity'], $var->id);
                        $request->session('cart')->put('cart', $cart);
                    }
                }
            }
        }
        return view('User.profile.shoppingcart');
    }
    public function buyNow(Request $request)
    {
        $variant = ProductVariant::find($request->id);
        $quantity = $request->quantity;
        if ($variant == null || $variant->status == 0 || $variant->stock == 0) {
            return response()->json([
                'success' => 0,
                'message' => 'Sản phẩm hết hàng'
            ]);
        } else {
            $buyNow = ['quantity' => $quantity, 'totalPrice' => $quantity * $variant->price, 'product_info' => $variant->product, 'variant_info' => $variant];
            $request->session()->put('buy-now', $buyNow);
            return response()->json([
                'url' => route('user.payment'),
                'success' => 1
            ]);

        }
    }
    public function addToCart(Request $request)
    {
        $productId = $request->product_id;
        $sizeId = $request->size_id;
        $quantity = $request->quantity;
        $note = $request->note;
        $toppings = $request->toppings ?? []; // mảng các topping_id
        $toppingQuantities = $request->topping_quantities ?? []; // mảng quantity tương ứng

        $product = Product::find($productId);
        $size = Size::find($sizeId);

        if (!$product || !$size) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm hoặc size không hợp lệ']);
        }

        // Tính giá size
        $productSize = ProductSize::where('product_id', $productId)->where('size_id', $sizeId)->first();
        $price = $productSize->price ?? $product->price;

        // Tính tổng giá topping
        $totalToppingPrice = 0;
        $toppingDetails = [];
        foreach ($toppings as $index => $toppingId) {
            $topping = Topping::find($toppingId);
            $qty = $toppingQuantities[$index] ?? 1;
            if ($topping) {
                $toppingPrice = $topping->price * $qty;
                $totalToppingPrice += $toppingPrice;
                $toppingDetails[] = [
                    'id' => $topping->id,
                    'name' => $topping->name,
                    'price' => $topping->price,
                    'quantity' => $qty,
                ];
            }
        }

        $cart = session('cart', []);
        $cartItem = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'size_id' => $sizeId,
            'size_name' => $size->name,
            'quantity' => $quantity,
            'note' => $note,
            'price_per_item' => $price,
            'toppings' => $toppingDetails,
            'total_price' => ($price + $totalToppingPrice) * $quantity,
        ];

        $cart[] = $cartItem;
        session(['cart' => $cart]);

        return response()->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng', 'cart' => $cart]);
    }

    public function deleteItemCart(Request $request, string $variant_id)
    {
        $variant = ProductVariant::find($variant_id);
        $cart = session('cart') ? session('cart') : null;
        if (array_key_exists($variant_id, $cart->listProductVariants) == false) {
            return response()->json([
                'sussess' => false,
                'message' => 'giỏ hàng chưa có sản phẩm này!'
            ]);
        }
        if ($cart == null)
            return response()->json([
                'sussess' => false,
                'giỏ hàng chưa có sản phẩm!'
            ]);
        $cart->deleteItemCart($variant_id);
        if ($cart->totalQuantity == 0) {
            $request->session()->forget('cart');
            return response()->json([
                'sussess' => true,
                'message' => 'Đã xóa ' . $variant->product->name . ' (' . $variant->color . '/' . $variant->internal_memory . ') khỏi giỏ hàng',
                'cart' => ['totalQuantity' => $cart->totalQuantity, 'totalPrice' => $cart->totalPrice]
            ]);
        } else
            $request->session()->put('cart', $cart);
        return response()->json([
            'sussess' => true,
            'message' => 'Đã xóa ' . $variant->product->name . ' (' . $variant->color . '/' . $variant->internal_memory . ') khỏi giỏ hàng',
            'cart' => ['totalQuantity' => $cart->totalQuantity, 'totalPrice' => $cart->totalPrice]
        ]);
    }
    public function deleteAllItem(Request $request)
    {
        if (session('cart') != null) {
            $request->session()->forget('cart');
            return 'Đã xóa tất cả sản phẩm trong giỏ hàng!';
        }
        return 'giỏ hàng chưa có sản phẩm!';
    }

    public function minusOnQuantity(Request $request, $variant_id)
    {
        $cart = session('cart') ? session('cart') : null;
        if ($cart == null)
            return 'giỏ hàng chưa có sản phẩm!';
        if (array_key_exists($variant_id, $cart->listProductVariants) == false) {
            return response()->json([
                'sussess' => false,
                'message' => 'giỏ hàng chưa có sản phẩm này!'
            ]);
        }
        $cart->minusOnQuantity($variant_id);
        $request->session()->put('cart', $cart);
        return response()->json([
            'success' => true,
            'cart' => ['totalQuantity' => $cart->totalQuantity, 'totalPrice' => $cart->totalPrice]
        ]);

    }
}
