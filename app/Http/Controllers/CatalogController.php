<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ItemCategory;
use App\Item;

class CatalogController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::whereHas('items')->get();
        return view('catalog', compact(['categories']));
    }

    public function show(Request $request)
    {
        $items = Item::where(function($where) use ($request){
            if ($request->has('keyword')) {
                $where->where('item_description', 'like', '%'.$request->keyword.'%')
                    ->orWhere('item_code', 'like', '%'.$request->keyword.'%')
                    ->orWhere('item_spesification', 'like', '%'.$request->keyword.'%')
                    ->orWhereHas('tags', function($where)use($request){
                        $where->where('name', 'like', '%'.$request->keyword.'%');
                    });
            }

            if (!empty($request->category)) {
                $where->where('item_category_id', $request->category);
            }
        })->get();

        $categories = ItemCategory::get();
        $category = !empty($request->category) ?  ItemCategory::find($request->category)->category_name : null;

        return view('catalog.show', compact(['items', 'categories', 'category']));
    }
}
