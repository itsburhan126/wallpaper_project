<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FilePath;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'status' => 'boolean',
        ]);

        $imagePath = $request->file('image')->store(FilePath::FOLDER_BANNERS, 'public');

        Banner::create([
            'title' => $request->title,
            'image' => $imagePath,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete(FilePath::getRelativePath($banner->image));
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:banners,id',
        ]);

        $banners = Banner::whereIn('id', $request->ids)->get();

        foreach ($banners as $banner) {
            if ($banner->image) {
                Storage::disk('public')->delete(FilePath::getRelativePath($banner->image));
            }
            $banner->delete();
        }

        return response()->json(['success' => true, 'message' => 'Selected banners deleted successfully.']);
    }
}
