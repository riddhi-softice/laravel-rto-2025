<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class BlogApiController extends BaseController
{
    public function blog_list(Request $request)
    {
        $cate_id = $request->input('cate_id', 0);
        $search_text = $request->input('search_text', '');
        $page = $request->input('page_no', 1);
        $perPage = 10;
        
        // $query = DB::connection('mysql3')->table('blogs');
        $query = DB::table('blogs');
        if ($cate_id) {
            $query->where('tag_id', $cate_id);
        }
        if ($search_text) {
            $query->where('title', 'like', '%' . $search_text . '%');
            // $query->orWhere('content', 'like', '%' . $search_text . '%');
        }
        /* $blogs = $query
            ->select('id', 'title', 'image_url', 'created_at')
            ->orderBy('top_status', 'desc')->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page); */

        # position wise data get 1,2,3,4,5,0,0,0,0
        $blogs = $query
            ->select('id', 'title', 'image_url', 'created_at')
            ->orderByRaw('CASE 
                            WHEN top_status = 0 THEN 1 
                            ELSE 0 
                        END, 
                        top_status ASC') // Ensure 1,2,3,4,5 order
            ->orderBy('id', 'desc')  // Order by ID within each group
            ->paginate($perPage, ['*'], 'page', $page);

        $data = $blogs->getCollection()->map(function ($blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title ?? "",
                'image_url' => $blog->image_url 
                    ? asset('public/'. $blog->image_url) 
                    : "",
                'created_at' => Carbon::parse($blog->created_at)->format('d-M-Y'),
            ];
        });
        $paginationDetails = [
            'total_record' => $blogs->total(),
            'per_page' => $blogs->perPage(),
            'current_page' => $blogs->currentPage(),
            'last_page' => $blogs->lastPage(),
        ];

        $responseData['pagination'] = $paginationDetails;
        $responseData['result_data'] = $data;
        $response = $this->encryptData($responseData);

        $encryptedResponse = $this->encryptData($responseData);
        return $this->sendResponse($encryptedResponse, 'Blogs retrieved successfully.');
    }

    public function blog_details(Request $request)
    {
        $blog_id = $request->input('blog_id', 0);
        $blog = DB::table('blogs')
            ->select('id','title','content','image_url','created_at','tag_id')
            ->where('id', $blog_id)->first();
        if (!$blog) {
            return $this->sendResponse([], 'Blog not found.', false, 404);
        }

         # if category id not exist than set latest 3 data
        $query = DB::table('blogs')
            ->select('id', 'title', 'image_url', 'created_at')
            ->where('id', '!=', $blog_id); 

        # multiple stored and multiple search, coma seperated
        if (!empty($blog->tag_id)) {
            $tagIds = explode(',', $blog->tag_id);
            $query->where(function ($q) use ($tagIds) {
                foreach ($tagIds as $tagId) {
                    $q->orWhereRaw("FIND_IN_SET(?, tag_id)", [$tagId]);
                }
            });
        }
        $relatedBlogs = $query->orderBy('id', 'desc')->take(3)->get();

        if ($relatedBlogs->isEmpty()) {
            $relatedBlogs = DB::table('blogs')
                ->select('id', 'title', 'image_url', 'created_at')
                ->where('id', '!=', $blog_id)
                ->orderBy('id', 'desc')
                ->take(3)
                ->get();
        }
        $content = $blog->content;
        $content = htmlspecialchars_decode($content, ENT_QUOTES);
        $content = str_replace('data-src', 'src', $content);
        $content = stripslashes($content);
        $blogData = [
            'id' => $blog->id,
            'title' => $blog->title,
            'content' => $content,
            'image_url' => $blog->image_url  ? asset('public/'. $blog->image_url) : "",
            'created_at' => Carbon::parse($blog->created_at)->format('d-M-Y'),
        ];
        $relatedBlogsData = $relatedBlogs->map(function ($related) {
            return [
                'id' => $related->id,
                'title' => $related->title,
                'image_url' => $related->image_url  ? asset('public/'. $related->image_url) : "",
                'created_at' => Carbon::parse($related->created_at)->format('d-M-Y'),
            ];
        });
        $responseData = [
            'blog' => $blogData,
            'related_blogs' => $relatedBlogsData,
        ];
        $encryptedResponse = $this->encryptData($responseData);
        return $this->sendResponse($encryptedResponse, 'Blog data fetched successfully.');
    }

    public function blog_category_list(Request $request)
    {
        $categories = DB::table('tags')->select('id','name')->orderBy('id','desc')->get();
        $responseData = $categories->map(function ($cat) {
            return [
                'cat_id' => $cat->id,
                'cat_name' => $cat->name ?: "",
            ];
        });
        $response = $this->encryptData($responseData);
        return $this->sendResponse($response, 'Data retrieved successfully.');
    }
    
    public function home_blog_list(Request $request)
    {
        $response = DB::table('blogs')->select('id','title','image_url','created_at')
        // ->orderBy('top_status','desc')
        ->orderByRaw('CASE 
                            WHEN top_status = 0 THEN 1 
                            ELSE 0 
                        END, 
                        top_status ASC')
        ->orderBy('id', 'desc')->take(5)->get();
        
        $responseData = $response->map(function ($data) {
            return [
                'id' => $data->id,
                'title' => $data->title ?: "",
                'image_url' => $data->image_url ? asset('public/'. $data->image_url) : "",
                'created_at' => Carbon::parse($data->created_at)->format('d-M-Y'),
            ];
        });

        $encryptedResponse = $this->encryptData($responseData);
        return $this->sendResponse($encryptedResponse, 'Data retrieved successfully.');
    }

}
