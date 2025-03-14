<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartManagement
{
    // add item to cart
    static public function addItemToCart($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        $exsiting_item = null;
        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $exsiting_item = $item;
                break;
            }
        }
        if ($exsiting_item !== null) {
            $cart_items[$key]['quantity']++;
            $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['price'];
        } else {

            $product = Product::where('id', $product_id)->first(['id', 'name', 'price', 'images']);
            if ($product) {
                $cart_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->images[1],
                    'quantity' => 1,
                    'total_amount' => $product->price
                ];
            }
        }
        self::addCartItemToCookie($cart_items);
        return count($cart_items);
    }

    // add item to cart with Quantity
    static public function addItemToCartWithQty($product_id, $qty = 1)
    {
        $cart_items = self::getCartItemsFromCookie();

        $exsiting_item = null;
        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $exsiting_item = $item;
                break;
            }
        }
        if ($exsiting_item !== null) {
            $cart_items[$key]['quantity'] = $qty;
            $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['price'];
        } else {

            $product = Product::where('id', $product_id)->first(['id', 'name', 'price', 'images']);
            if ($product) {
                $cart_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->images[1],
                    'quantity' => $qty,
                    'total_amount' => $product->price
                ];
            }
        }
        self::addCartItemToCookie($cart_items);
        return count($cart_items);
    }

    // remove item from cart
    static public function removeCartItem($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                unset($cart_items[$key]);
            }
        }
        self::addCartItemToCookie($cart_items);
        return $cart_items;
    }
    // add cart item to cookie
    static public function addCartItemToCookie($cart_items)
    {
        Cookie::queue('cart_items', json_encode($cart_items), 60 * 24 * 30);
    }
    // clear cart item from cookie
    static public function clearCartItemFromCookie()
    {
        Cookie::queue(Cookie::forget('cart_items'));
    }
    // get all cart item from cookie
    static public function getCartItemsFromCookie()
    {
        $cart_items = json_decode(Cookie::get('cart_items'), true);
        if (!$cart_items) {
            $cart_items = [];
        }
        return $cart_items;
    }
    // increment item quantity
    static public function incrementQuantityToCartItem($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();
        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $cart_items[$key]['quantity']++;
                $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['price'];
            }
        }
        self::addCartItemToCookie($cart_items);
        return $cart_items;
    }
    // decrement item quantity
    static public function decrementQuantityToCartItem($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();
        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                if ($cart_items[$key]['quantity'] > 1) {
                    $cart_items[$key]['quantity']--;
                    $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['price'];
                }
            }
        }
        self::addCartItemToCookie($cart_items);
        return $cart_items;
    }

    // calculate grand total
    static public function calculateGrandTotal($items)
    {
        return array_sum(array_column($items, 'total_amount'));
    }
}
