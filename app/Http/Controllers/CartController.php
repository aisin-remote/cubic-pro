<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cart;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::where('user_id', auth()->user()->id)
                    ->get();

        return view('cart', compact(['carts']));
    }

    public function update($id, Request $request)
    {
        $cart = Cart::find($id);
        $cart->qty = $request->qty;
        $cart->total = $cart->qty * $cart->price;
        $cart->save();

        $total = Cart::where('user_id', auth()->user()->id)
                    ->get();

        $total_formatted = number_format($cart->pluck('total')->sum());
        $cart->price_formatted = number_format($cart->price);
        $cart->total_formatted = number_format($cart->total);

        return response()->json(['data' => $cart, 'total' => $total_formatted]);
    }

    public function delete($id)
    {
        $cart = Cart::find($id);
        $cart->delete();

        $total = Cart::where('user_id', auth()->user()->id)
                ->get();

        $total_formatted = number_format($cart->pluck('total')->sum());
        return response()->json(['total' => $total_formatted]);
    }
	public function deleteCart($id)
	{
		$item = Cart::where('user_id', auth()->user()->id)->where('item_id', $id)->delete();
		return response()->json(['message'=>'Deleting is success']);
	}
    public function checkout(Request $request)
    {
        if ($request->budget_type == 'capex') {
            return redirect('approval/create/cx/add');
        } else if($request->budget_type == 'expense'){
            return redirect('approval/create/ex/add');
        }else{
			 return redirect('approval/create/ub/add');
		}
    }
}
