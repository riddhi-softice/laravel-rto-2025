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
        $cacheKey = "videos_page";
        $responseData = Cache::remember($cacheKey, 60, function () {
            $result = DB::table('videos')->get();
            return $result->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'video_url' => url('storage/app/public/compressed_videos/' . $video->video_url), // Generate full URL for the video
                    'video_button' => $video->video_button,
                    'button_link' => $video->button_link,
                ];
            });
        });
        

        $paginationDetails = [
            'total_record' => 1,
            'per_page' => 1,
            'current_page' => 1,
            'last_page' => 1,
        ];

        $selected_reels = DB::table('videos')->where('top_status', '!=', 0)->get();
        $selected_data = $selected_reels->map(function ($value) {
            return [
                'id' => $value->id,
                'title' => $value->title,
                'video_url' => url('storage/app/public/compressed_videos/' . $value->video_url),
                'video_button' => $value->video_button,
                'button_link' => $value->button_link,
            ];
        });

        $data = [
            'pagination' => $paginationDetails,
            'selected_reels' => $selected_data,
            'result_data' => $responseData
        ];
        $response = $this->encryptData($data);
        return response()->json(['success' => true,'message' => "Data get successfully",'data' => $data]);
    }

    /* public function get_video(Request $request)
    {
        $perPage = 50; 
        $page = $request->page_no ?? 1;
        $cacheKey = "videos_page_{$page}_perPage_{$perPage}";
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
    } */

    public function uploadAndCompressVideo(Request $request) 
    {
        // dd($request->file('video'));
        try {
            // Initialize Cloudinary configuration
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => 'dik4xtm4s',
                    'api_key' => '241435933754747',
                    'api_secret' => 'hDinCDTiShWi34BdXhuxw0VgMLA',
                ],
            ]);
    
            // // Upload and compress the video , down clearity
            // $uploadedVideo = $cloudinary->uploadApi()->upload(
            //     $request->file('video')->getPathname(),
            //     [
            //         'resource_type' => 'video',
            //         'quality' => 'auto',
            //         // 'bit_rate' => '500000', // Approximation to target file size
            //         // 'max_bytes' => 5242880, // 5MB in bytes
            //     ]
            // );

            $uploadedVideo = $cloudinary->uploadApi()->upload(
                $request->file('video')->getPathname(),
                [
                    'resource_type' => 'video',
                    'transformation' => [
                        [
                            'quality' => 'auto:best', // Retain the best possible quality
                            'bit_rate' => '800000', // Balanced bitrate for clarity (800 kbps)
                            'fps' => '24', // Standard frame rate
                            'width' => '1280', // Adjust resolution to HD (1280x720)
                            'height' => '720',
                            'crop' => 'limit', // Ensures the video fits within dimensions without distortion
                        ],
                    ],
                ]
            );
            // Get the video URL from Cloudinary response
            $videoUrl = $uploadedVideo['secure_url'];
            // Fetch the video content from the URL
            $videoContent = file_get_contents($videoUrl);
            // Define the storage path for compressed videos
                
            /* $storagePath = storage_path('app/public/compressed_videos');
                // Ensure the directory exists
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }
            $localFileName = uniqid() .'.mp4';
            file_put_contents($storagePath . '/' . $localFileName, $videoContent);*/
            
            $directory = 'compressed_videos';
            $fileName = time() . '.' . $videoContent->getClientOriginalExtension();
            $path = $videoContent->storeAs($directory, $fileName, 'public');
    
            return response()->json([
                'message' => 'Video uploaded and saved successfully!',
                'cloudinary_url' => $videoUrl,
                'local_path' => $storagePath . '/' . $localFileName,
            ]);
        } catch (ApiError $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
