<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->filled('product_slug')) {
            $query->where('product_slug', $request->product_slug);
        }

        $banners = $query->where('status', 'A')
        ->orderBy('position')
        ->get()
        ->map(function ($banner) {
            $banner->image_url = asset('storage/' . $banner->image);
            return $banner;
        });


        return response()->json($banners);
    }
}
