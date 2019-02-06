<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ItemCategory;
use App\Item;
use App\Cart;

class CatalogController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::with(['items' => function($query){
            
                $query->groupBy('item_code');
            
        }])->whereHas('items')->get();

        return view('catalog', compact(['categories']));
    }

    public function show(Request $request)
    {
        $items = Item::where(function($where) use ($request){
            if ($request->has('keyword')) {
                $where->where('item_description', 'like', '%'.$request->keyword.'%')
                    ->orWhere('item_code', 'like', '%'.$request->keyword.'%')
                    ->orWhere('item_specification', 'like', '%'.$request->keyword.'%')
                    ->orWhereHas('tags', function($where)use($request){
                        $where->where('name', 'like', '%'.$request->keyword.'%');
                    });
            }

            if (!empty($request->category)) {
                $where->where('item_category_id', $request->category);
            }
        })
        ->groupBy('item_code')
        ->paginate(15);

        $categories = ItemCategory::get();
        $category = !empty($request->category) ?  ItemCategory::find($request->category)->category_name : null;

        return view('catalog.show', compact(['items', 'categories', 'category']));
    }

    public function details($item_code)
    {
        $items = Item::where('item_code', $item_code)
                    ->get();
        
        return view('catalog.details', compact(['items']));
    }

    public function store(Request $request)
    {
        if (auth()->check()) {

            $item = Item::find($request->item_id);

            $cart = new Cart;
            $cart->user_id = auth()->user()->id;
            $cart->item_id = $item->id;
            $cart->qty = $request->qty;
            $cart->price = $item->item_price;
            $cart->total = $item->item_price * $request->qty;
            $cart->reason = $request->reason;
            $cart->save();

            return redirect()->back();

        } else {
            return redirect('login');
        }
    }
}
