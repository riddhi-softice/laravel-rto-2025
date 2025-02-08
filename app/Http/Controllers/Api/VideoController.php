<?php

namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use DB;
use Illuminate\Support\Facades\Cache;

class VideoController extends BaseController
{
    public function get_home_video(Request $request)
    {
        $totalVideos = DB::table('videos')->count();
        $randomOffset = $totalVideos > 5 ? rand(0, $totalVideos - 5) : 0;

        $videos = DB::table('videos')->offset($randomOffset)->limit(5)->get();
        $result_data = $videos->map(function ($video) {
            return [
                'id' => $video->id,
                'title' => $video->title,
                'video_url' => url('storage/app/public/compressed_videos/' . $video->video_url),
                'video_button' => $video->video_button,
                'button_link' => $video->button_link,
                //'thumbnail' => url('storage/thumbnail/' . $video->thumbnail),
            ];
        });
        $response = $this->encryptData($result_data);

        return response()->json([
            'success' => true,
            'message' => "Data retrieved successfully",
            'data' => $response,
        ]);
    }

    public function get_video(Request $request)
    {
        $perPage = 50; 
        $page = $request->page_no ?? 1;
        $cacheKey = "videos_page_{$page}_perPage_{$perPage}";
        // $result = DB::table('videos')->paginate($perPage, ['*'], 'page', $page);
        $responseData = Cache::remember($cacheKey, 60, function () use ($perPage, $page) {
            $result = DB::table('videos')->paginate($perPage, ['*'], 'page', $page);
            
            $result_data = $result->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'video_url' => url('storage/app/public/compressed_videos/' . $video->video_url), // Generate full URL for the video
                    'video_button' => $video->video_button,
                    'button_link' => $video->button_link,
                ];
            });
            $paginationDetails = [
                'total_record' => $result->total(),
                'per_page' => $result->perPage(),
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
            ];
            return [
                'pagination' => $paginationDetails,
                'result_data' => $result_data
            ];
        });
        $response = $this->encryptData($responseData);
        return response()->json(['success' => true,'message' => "Data get successfully",'data' => $response]);
    }
}
