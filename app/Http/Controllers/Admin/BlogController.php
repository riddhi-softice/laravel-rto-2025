<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Tag;
use Carbon\Carbon;
use DB;
use Yajra\DataTables\DataTables; // For DataTables (optional, if using a package)


class BlogController extends Controller
{
    public function index_()
    {
        $blogs = Blog::orderBy('id','desc')->get();
        return view('blogs.index', compact('blogs'));
    }

    public function index(Request $request) #  _ajax_Working
    {
        if ($request->ajax()) {
            $data = Blog::query(); 
            // $data = Blog::orderBy('publish_at','desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image_url', function($row){
                    $url = asset('public/'.$row->image_url);
                    $btn = '<img src="' . $url . '" alt="Profile Picture" width="50" height="50">';
                    return $btn;
                })
                ->addColumn('created_at', function($row){
                    return Carbon::parse($row->created_at)->format('d M Y');
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="'.route('blogs.edit', $row->id).'" class="btn btn-sm btn-warning">Edit</a> &nbsp;';
                    $btn .= '<button data-id="'.$row->id.'" data-toggle="modal" data-target="#confirmDeleteModal" class="btn btn-danger btn-sm deleteData">Delete</button>';
                    return $btn;
                })
                ->addColumn('top_status', function ($row) {
                    $checked = $row->top_status != 0 ? 'checked' : '';
                    $checkbox = '<input type="checkbox" class="top-status" data-id="'.$row->id.'" '.$checked.'>';
                    return $checkbox;
                })
                ->rawColumns(['action','image_url','created_at','top_status'])
                ->make(true);
        }
        return view('blogs.ajaxindex');
    }

    public function create()
    {
        $tags = Tag::all(); 
        return view('blogs.create', compact('tags'));
    } 

    public function store(Request $request)
    {
        // dd($request->all());
        $data = $request->only(['title', 'content', 'notification_url','created_at']);
        $data['image_url'] = $blog->image_url ?? 'img/default_img.jpg';
       
        if ($request->hasFile('image_url')) {
            $image = $request->file('image_url');
            $directory = 'img/image_urls';
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path($directory), $imageName);
            $data['image_url'] = $directory . '/' . $imageName;
        }

        if($request->tag_id){
            $data['tag_id']  = implode(',',$request->tag_id);
        }
        
        $data['publish_at'] = now();
        DB::table('blogs')->insert($data);

        return redirect()->route('blogs.index')->with('success',"Blog added successfully");
    }

    public function edit(Blog $blog)
    {
        $tags = Tag::all(); 
        $selectedTags = explode(',',$blog->tag_id);
        return view('blogs.edit', compact('tags','blog', 'selectedTags'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->only(['title', 'content', 'notification_url','created_at']);
        $data['image_url'] = $blog->image_url ?? 'img/default_img.jpg';

        if ($request->hasFile('image_url')) {

            if ($blog->image_url && file_exists(public_path($blog->image_url))) {
                unlink(public_path($blog->image_url));
            }
            $image = $request->file('image_url');
            $directory = 'img/image_urls';
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path($directory), $imageName);
            $data['image_url'] = $directory . '/' . $imageName;
        }
    
        if($request->tag_id){
            $data['tag_id']  = implode(',',$request->tag_id);
        }

        $data['publish_at'] = now();
        $blog->update($data);
        return redirect()->route('blogs.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy($id)
    {
        $blog = Blog::where('id',$id)->first();
        if ($blog->image_url && file_exists(public_path($blog->image_url))) {
            unlink(public_path($blog->image_url));
        }
        $blog->delete();
        return redirect()->route('blogs.index')->with('success', 'notification deleted successfully.');
    }

    public function updateTopStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:blogs,id',
            'top_status' => 'required|boolean',
        ]);
        $top_status = 0;
        $last_position = Blog::orderBy('top_status','desc')->value('top_status');
        if($validated['top_status']) {  # if top status selected
            $top_status = $last_position + 1;
        }

        $blog = Blog::findOrFail($validated['id']);
        $blog->top_status = $top_status;
        $blog->save();

        return response()->json(['success' => true]);
    }

}

