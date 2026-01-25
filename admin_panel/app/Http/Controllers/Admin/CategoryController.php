<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Category::count(),
            'active' => Category::where('status', true)->count(),
            'inactive' => Category::where('status', false)->count(),
        ];
        $categories = Category::latest()->paginate(20);
        return view('admin.categories.index', compact('categories', 'stats'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->status = $request->has('status');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file->getClientOriginalName());
            $file->move(public_path('img/categories'), $filename);
            $category->image = 'img/categories/' . $filename;
        }

        $category->save();

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->status = $request->has('status');

        if ($request->hasFile('image')) {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file->getClientOriginalName());
            $file->move(public_path('img/categories'), $filename);
            $category->image = 'img/categories/' . $filename;
        }

        $category->save();

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        if ($category->image && File::exists(public_path($category->image))) {
            File::delete(public_path($category->image));
        }
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
        ]);

        $categories = Category::whereIn('id', $request->ids)->get();

        foreach ($categories as $category) {
            if ($category->image && File::exists(public_path($category->image))) {
                File::delete(public_path($category->image));
            }
            $category->delete();
        }

        return response()->json(['success' => true, 'message' => 'Selected categories deleted successfully.']);
    }
}
