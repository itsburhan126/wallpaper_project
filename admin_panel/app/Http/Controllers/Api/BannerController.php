<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FilePath;

class BannerController extends Controller
{
    // Public API for App
    public function index()
    {
        $banners = Banner::where('status', true)->latest()->get();
        
        $data = $banners->map(function ($banner) {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'image_url' => FilePath::getUrl($banner->image),
                'status' => $banner->status,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Admin API to add banner
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'title' => 'nullable|string'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store(FilePath::FOLDER_BANNERS, 'public');

            $banner = Banner::create([
                'title' => $request->title,
                'image' => $path,
                'status' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Banner created successfully',
                'data' => $banner
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Image upload failed'], 400);
    }
}
