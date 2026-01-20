<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Helpers\FilePath;

class WallpaperController extends Controller
{
    public function index(Request $request)
    {
        $query = Wallpaper::where('status', true)->with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('featured') && $request->featured == 1) {
            // Assuming we might add 'is_featured' later, or sort by views/downloads
            $query->orderBy('views', 'desc');
        } else {
            $query->latest();
        }

        $wallpapers = $query->paginate(20);

        // Transform image paths to full URLs
        $wallpapers->getCollection()->transform(function ($wallpaper) {
            $wallpaper->image = FilePath::getUrl($wallpaper->image);
            $wallpaper->thumbnail = FilePath::getUrl($wallpaper->thumbnail);
            return $wallpaper;
        });

        return response()->json([
            'status' => true,
            'data' => $wallpapers
        ]);
    }

    public function show($id)
    {
        $wallpaper = Wallpaper::where('status', true)->with('category')->find($id);

        if (!$wallpaper) {
            return response()->json([
                'status' => false,
                'message' => 'Wallpaper not found'
            ], 404);
        }

        // Increment views
        $wallpaper->increment('views');

        // Transform image paths
        $wallpaper->image = FilePath::getUrl($wallpaper->image);
        $wallpaper->thumbnail = FilePath::getUrl($wallpaper->thumbnail);

        return response()->json([
            'status' => true,
            'data' => $wallpaper
        ]);
    }

    public function download($id)
    {
        $wallpaper = Wallpaper::where('status', true)->find($id);

        if (!$wallpaper) {
            return response()->json([
                'status' => false,
                'message' => 'Wallpaper not found'
            ], 404);
        }

        // Increment downloads
        $wallpaper->increment('downloads');

        return response()->json([
            'status' => true,
            'message' => 'Download counted'
        ]);
    }

    public function getCategories()
    {
        $categories = Category::where('status', true)->get();

        $categories->transform(function ($category) {
            $category->image = FilePath::getUrl($category->image);
            return $category;
        });

        return response()->json([
            'status' => true,
            'data' => $categories
        ]);
    }
}
