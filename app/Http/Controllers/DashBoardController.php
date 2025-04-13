<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    /**
     * Display the dashboard view with a list of products and their images.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = Product::with(relations: 'images')->latest()->paginate(6);
        return view('dashboard',compact('products'));
    }
}
