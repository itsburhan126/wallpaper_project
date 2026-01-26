<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Short;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShortController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Short::count(),
            'active' => Short::where('is_active', true)->count(),
            'views' => Short::sum('views'),
        ];
        $shorts = Short::latest()->paginate(10);
        return view('admin.shorts.index', compact('shorts', 'stats'));
    }

    public function create()
    {
        return view('admin.shorts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'video' => 'nullable|required_without:youtube_url|mimes:mp4,mov,ogg,qt|max:102400', // 100MB max
            'youtube_url' => 'nullable|required_without:video|url|max:255',
            'thumbnail' => 'nullable|image|max:10240',
        ]);

        $short = new Short();
        $short->title = $request->title;
        $short->is_active = $request->boolean('is_active');

        if ($request->filled('youtube_url')) {
            $short->video_url = $request->youtube_url;
        } elseif ($request->hasFile('video')) {
            $path = $request->file('video')->store('shorts', 'public');
            $short->video_url = Storage::url($path);
        }

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('shorts/thumbnails', 'public');
            $short->thumbnail_url = Storage::url($path);
        }

        $short->save();

        return redirect()->route('admin.shorts.index')->with('success', 'Short video uploaded successfully.');
    }

    public function destroy($id)
    {
        $short = Short::findOrFail($id);
        // Delete files
        if ($short->video_url) {
            $videoPath = str_replace('/storage/', '', $short->video_url);
            Storage::disk('public')->delete($videoPath);
        }
        if ($short->thumbnail_url) {
            $thumbPath = str_replace('/storage/', '', $short->thumbnail_url);
            Storage::disk('public')->delete($thumbPath);
        }
        
        $short->delete();
        return redirect()->back()->with('success', 'Short deleted successfully.');
    }
}
