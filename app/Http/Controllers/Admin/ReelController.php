<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reel;
use Carbon\Carbon;
use DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;

class ReelController extends Controller
{   
    public function index(Request $request) #  _ajax_working
    {
        if ($request->ajax()) {
            $data = Reel::query();
            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('created_at', function($row){
                //     return Carbon::parse($row->created_at)->format('d M Y');
                // })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : null; // Ensure correct format
                })
                ->addColumn('action', function($row){
                    $url = asset('storage/app/public/compressed_videos/' . $row->video_url);
                    $btn = '<button data-url="'.$url.'" data-toggle="modal" data-target="#ViewVideo" class="btn btn-secondary btn-sm viewData" style="margin-bottom: 3px;" title="View"><i class="bi bi-eye"></i></button> &nbsp';
                    $btn .= '<a href="'.route('reels.edit', $row->id).'" class="btn btn-sm btn-warning" style="margin-bottom: 3px;" title="Edit"><i class="bi bi-pencil"></i></a> &nbsp;';
                    $btn .= '<button data-id="'.$row->id.'" data-toggle="modal" data-target="#confirmDeleteModal" class="btn btn-danger btn-sm deleteData" title="Delete"><i class="bi bi-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                // ->rawColumns(['action','created_at'])
                ->make(true);
        }
        return view('reels.ajaxindex');
    }

    public function create()
    {
        return view('reels.create');
    } 

    # without compress file
    public function store_old(Request $request)
    {
        $data = $request->only(['title', 'video_button', 'button_link']);
        if ($request->hasFile('video_url')) {
            $video = $request->file('video_url');
            $directory = 'compressed_videos';
            $fileName = time() . '.' . $video->getClientOriginalExtension();
            // $video->move(public_path($directory), $fileName);
            $path = $video->storeAs($directory, $fileName, 'public');
            $data['video_url'] = $fileName;
        }
        DB::table('videos')->insert($data);
        return redirect()->route('reels.index')->with('success',"Reel added successfully");
    }

    # with compress file
    public function store(Request $request) 
    {
        // dd($request->is_compress == "on");
        $data = $request->only(['title', 'video_button', 'button_link','is_compress']);
        if ($request->hasFile('video_url')) {

            if($request->is_compress == "on"){
                // Initialize Cloudinary configuration
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => 'dik4xtm4s',
                        'api_key' => '241435933754747',
                        'api_secret' => 'hDinCDTiShWi34BdXhuxw0VgMLA',
                    ],
                ]);

                $uploadedVideo = $cloudinary->uploadApi()->upload(
                    $request->file('video_url')->getPathname(),
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
                $storagePath = storage_path('app/public/compressed_videos');
                // Ensure the directory exists
                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }
                $localFileName = time() .'.mp4';
                file_put_contents($storagePath . '/' . $localFileName, $videoContent);
            }else{
                $video = $request->file('video_url');
                $directory = 'compressed_videos';
                $localFileName = time() . '.' . $video->getClientOriginalExtension();
                $path = $video->storeAs($directory, $localFileName, 'public');
            }
            $data['video_url'] = $localFileName;
        }
        DB::table('videos')->insert($data);
        return redirect()->route('reels.index')->with('success',"Reel added successfully");
    }

    public function edit(Reel $reel)
    {
        return view('reels.edit', compact('reel'));
    }

    public function update(Request $request, Reel $reel)
    {  
        $data = $request->only(['title', 'video_button', 'button_link']);
        $data['is_compress'] = $request->has('is_compress') ? 1 : 0;
        if ($request->hasFile('video_url')) {
            if ($reel->video_url && Storage::exists('public/compressed_videos/' . $reel->video_url)) {
                Storage::delete('public/compressed_videos/' . $reel->video_url);
            }
            if($request->is_compress == "on"){
                // Initialize Cloudinary configuration
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => 'dik4xtm4s',
                        'api_key' => '241435933754747',
                        'api_secret' => 'hDinCDTiShWi34BdXhuxw0VgMLA',
                    ],
                ]);
                $uploadedVideo = $cloudinary->uploadApi()->upload(
                    $request->file('video_url')->getPathname(),
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
                $storagePath = storage_path('app/public/compressed_videos');
                // Ensure the directory exists
                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }
                $localFileName = time() .'.mp4';
                file_put_contents($storagePath . '/' . $localFileName, $videoContent);
            }else{
                if ($request->hasFile('video_url')) {
                    $video = $request->file('video_url');
                    $directory = 'compressed_videos';
                    $localFileName = time() . '.' . $video->getClientOriginalExtension();
                    $path = $video->storeAs($directory, $localFileName, 'public');
                    $data['video_url'] = $localFileName;
                }
            }
            $data['video_url'] = $localFileName;
        }    
        $reel->update($data);
        return redirect()->route('reels.index')->with('success', 'Reel updated successfully.');
    }

    public function destroy($id)
    {
        $reel = Reel::where('id',$id)->first();
        // dd(Storage::exists('public/compressed_videos/' . $reel->video_url));
        if ($reel->video_url && Storage::exists('public/compressed_videos/' . $reel->video_url)) {
            Storage::delete('public/compressed_videos/' . $reel->video_url);
        }
        $reel->delete();
        return redirect()->route('reels.index')->with('success', 'Reel deleted successfully.');
    }

}

