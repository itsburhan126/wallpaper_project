<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        // Lazy seed default pages if they don't exist
        $defaults = [
            'Privacy Policy' => 'privacy-policy',
            'Terms & Conditions' => 'terms-conditions',
            'About App' => 'about-app',
        ];

        foreach ($defaults as $title => $slug) {
            if (!Page::where('slug', $slug)->exists()) {
                Page::create([
                    'title' => $title,
                    'slug' => $slug,
                    'content' => '<h2>' . $title . '</h2><p>Content coming soon...</p>',
                    'is_active' => true,
                ]);
            }
        }

        $pages = Page::all();
        return view('admin.pages.index', compact('pages'));
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $page = Page::findOrFail($id);
        $page->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully.');
    }
}
