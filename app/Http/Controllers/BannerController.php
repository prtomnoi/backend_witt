<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();
    
        if ($request->filled('product_slug')) {
            $query->where('product_slug', $request->product_slug);
        }
    
        $banners = $query->orderBy('product_slug')
                         ->orderBy('position')
                         ->paginate(10) // ปรับจำนวนต่อหน้าได้ตามต้องการ
                         ->withQueryString(); // คง query string เวลาเปลี่ยนหน้า
    
        return view('banners.index', compact('banners'));
    }
    
    public function create()
    {
        return view('banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_slug' => 'required|string|max:255',
            'image' => 'required|image',
            'link' => 'nullable|string',
            'position' => 'nullable|integer',
            'status' => 'required|in:A,I',
        ]);
    
        $file = $request->file('image');
        $ext = $file->getClientOriginalExtension();
    
        $filename = Str::slug($request->product_slug) . '-' . time() . '.' . $ext;
    
        $path = $file->storeAs('banners', $filename, 'public');
    
        Banner::create([
            'product_slug' => $request->product_slug,
            'image' => $path, 
            'link' => $request->link,
            'position' => $request->position ?? 1,
            'status' => $request->status,
        ]);
    
        return redirect()
        ->route('banners.index', ['product_slug' => $request->product_slug])
        ->with('success', 'Banner created successfully.');
    
    }


    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Banner deleted successfully.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->status = $request->input('status') === 'A' ? 'A' : 'I';
        $banner->save();

        return response()->json(['success' => true]);
    }

}
