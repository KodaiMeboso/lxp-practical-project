<?php

namespace App\Http\Controllers\Admin\Reviews;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Shop\Reviews\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::select('created_at', 'customer_id', 'evaluation', 'product_id', 'comment')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('admin.reviews.list', compact('reviews'));
    }
}
