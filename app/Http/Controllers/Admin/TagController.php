<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Blog;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::latest()->get();
        return view('tags.index', compact('tags'));
    }

    public function create()
    {
        return view('tags.create');
    }

    public function store(Request $request)
    {
        $input['name'] = $request->name;
        $Tag = Tag::create($input);

        return redirect()->route('tags.index')->with('success', 'Tag created successfully.');
    }

    public function edit(Tag $Tag)
    {
        return view('tags.edit', compact('Tag'));
    }

    public function update(Request $request, Tag $Tag)
    {
        $input['name'] = $request->name;
        $Tag->update($input);

        return redirect()->route('tags.index')->with('success', 'Tag updated successfully.');
    }

    public function destroy(Tag $Tag)
    {
        $blog = Blog::where('tag_id',$Tag->id)->first();
        if ($blog){
            if($blog->image_url && file_exists(public_path($blog->image_url))) {
                unlink(public_path($blog->image_url));
            }
            $blog->delete();
        }
        
        $Tag->delete();
        return response()->json(['message' => 'Item deleted successfully'], 200);
    }

}

