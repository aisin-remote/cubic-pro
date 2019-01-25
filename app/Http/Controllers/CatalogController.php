<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ItemCategory;

class CatalogController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::whereHas('items')->get();
        return view('catalog', compact(['categories']));
    }
}
