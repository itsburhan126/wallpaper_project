<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::latest()->paginate(20);
        return view('admin.games.index', compact('games'));
    }

    public function create()
    {
        return view('admin.games.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'url' => 'required|url',
            'play_time' => 'required|integer|min:1',
            'win_reward' => 'required|integer|min:0',
        ]);

        $game = new Game();
        $game->title = $request->title;
        $game->description = $request->description;
        $game->url = $request->url;
        $game->play_time = $request->play_time;
        $game->win_reward = $request->win_reward;
        $game->is_active = $request->has('is_active');
        $game->is_featured = $request->has('is_featured');
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file->getClientOriginalName());
            $file->move(public_path('img/games'), $filename);
            $game->image = 'img/games/' . $filename;
        }

        $game->save();

        return redirect()->back()->with('success', 'Game created successfully.');
    }

    public function edit($id)
    {
        $game = Game::findOrFail($id);
        return view('admin.games.edit', compact('game'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'url' => 'required|url',
            'play_time' => 'required|integer|min:1',
            'win_reward' => 'required|integer|min:0',
        ]);

        $game = Game::findOrFail($id);
        $game->title = $request->title;
        $game->description = $request->description;
        $game->url = $request->url;
        $game->play_time = $request->play_time;
        $game->win_reward = $request->win_reward;
        $game->is_active = $request->has('is_active');
        $game->is_featured = $request->has('is_featured');

        if ($request->hasFile('image')) {
            if ($game->image && File::exists(public_path($game->image))) {
                File::delete(public_path($game->image));
            }
            $file = $request->file('image');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\.]/', '_', $file->getClientOriginalName());
            $file->move(public_path('img/games'), $filename);
            $game->image = 'img/games/' . $filename;
        }

        $game->save();

        return redirect()->back()->with('success', 'Game updated successfully.');
    }

    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        if ($game->image && File::exists(public_path($game->image))) {
            File::delete(public_path($game->image));
        }
        $game->delete();

        return redirect()->back()->with('success', 'Game deleted successfully.');
    }
}
