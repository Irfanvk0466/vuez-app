<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class BidderController extends Controller
{
    /**
     * Display a listing of all registered bidders in the admin panel.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $bidders = User::where('role', 'bidder')->latest()->get();
        return view('admin.bidders.index', compact('bidders'));
    }
}
