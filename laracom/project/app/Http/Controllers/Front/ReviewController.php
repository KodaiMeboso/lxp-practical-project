<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Shop\Reviews\Review;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ReviewController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product_id = $request->input('product_id');
        $slug = $request->input('slug');

        if (Auth::check()) {
            $review = new Review;
            $review->product_id = $product_id;
            $review->customer_id = Auth::id();
            $review->evaluation = $request->input('evaluation');
            $review->comment = $request->input('comment');
            $review->save();
            session()->flash('success', '評価とコメントを投稿しました');
        }

        return back();
    }
}
