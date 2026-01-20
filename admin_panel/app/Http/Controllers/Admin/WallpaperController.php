<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallpaper;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WallpaperController extends Controller
{
    public function index()
    {
        $wallpapers = Wallpaper::with('category')->latest()->paginate(10);
        return view('admin.wallpapers.index', compact('wallpapers'));
    }

    public function create()
    {
        $categories = Category::where('status', true)->get();
        return view('admin.wallpapers.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $request->file('image')->store('wallpapers', 'public');
        
        // In a real app, you would generate a thumbnail here
        $thumbnailPath = $imagePath; 

        Wallpaper::create([
            'category_id' => $request->category_id,
            'image' => $imagePath,
            'thumbnail' => $thumbnailPath,
            'tags' => $request->tags,
            'status' => true,
        ]);

        return redirect()->route('admin.wallpapers.index')->with('success', 'Wallpaper created successfully.');
    }

    public function edit(Wallpaper $wallpaper)
    {
        $categories = Category::where('status', true)->get();
        return view('admin.wallpapers.edit', compact('wallpaper', 'categories'));
    }

    public function update(Request $request, Wallpaper $wallpaper)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'tags' => $request->tags,
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($wallpaper->image) {
                Storage::disk('public')->delete($wallpaper->image);
            }
            $imagePath = $request->file('image')->store('wallpapers', 'public');
            $data['image'] = $imagePath;
            $data['thumbnail'] = $imagePath;
        }

        $wallpaper->update($data);

        return redirect()->route('admin.wallpapers.index')->with('success', 'Wallpaper updated successfully.');
    }

    public function destroy(Wallpaper $wallpaper)
    {
        if ($wallpaper->image) {
            Storage::disk('public')->delete($wallpaper->image);
        }
        $wallpaper->delete();
        return redirect()->route('admin.wallpapers.index')->with('success', 'Wallpaper deleted successfully.');
    }
}
