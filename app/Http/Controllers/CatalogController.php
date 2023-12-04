<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ItemCategory;
use App\Item;
use App\Cart;
use PDF;

class CatalogController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::with(['items' => function($query){
                $query->groupBy('id');
            
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

        })
        ->groupBy('id')
        ->when(!empty($request->category), function($query) use ($request){
            $query->where('item_category_id', $request->category);
        })
        ->paginate(15);

        $categories = ItemCategory::get();
        $category = !empty($request->category) ?  ItemCategory::find($request->category)->category_name : null;

        return view('catalog.show', compact(['items', 'categories', 'category']));
    }

    public function details($id)
    {
        $items = Item::where('id', $id)
                    ->get();
        
        return view('catalog.details', compact(['items']));
    }

    public function store(Request $request)
    {
        if (auth()->check()) {

            $item = Item::find($request->item_id);

            if ($request->submit == 'cart') {

                $cart = Cart::firstOrNew(['item_id' => $item->id]);
                $cart->user_id = auth()->user()->id;
                $cart->item_id = $item->id;

                if ($cart->exists) {
                    $qty = $cart->qty + $request->qty;
                    $cart->qty = $qty;
                    $cart->total = $item->item_price * $qty;
                } else {
                    $cart->qty = $request->qty;
                    $cart->total = $item->item_price * $request->qty;
                }

                $cart->price = $item->item_price;
                $cart->reason = $request->reason;
                $cart->save();

                return redirect()->back();

            } else {

                $compare = [
                    'id' => $item->id,
                    'name' => $item->item_description,
                    'supplier' => $item->supplier->supplier_name,
                    'specification' => $item->item_specification,
                    'price' => $item->item_price,
                    'lead_times' => $item->lead_times,
                    'guarantee' => 'yes',
                    'technical_support' => 'yes',
                    'other' => 'money back for guarantee, free shiping, call center 24 hours'
                ];

                session()->put('compare.'.$item->id, $compare);
                return redirect()->back();           

            }

        } else {
            return redirect('login');
        }
    }

    public function compare()
    {
        $compares = session()->get('compare');
        return view('catalog.compare', compact(['compares']));
    }

    public function compareRemove($id)
    {
        session()->forget('compare.'.$id);
        return redirect()->back();
    }

    public function print()
    {

        $data['compares'] = session()->get('compare');
        $pdf = PDF::setPaper('a4', 'landscape')->loadView('pdf.comparison', $data);
        return $pdf->stream('comparison.pdf');

    }
}
