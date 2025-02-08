<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use App\Models\CommonSetting;
use App\Models\ApplicationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use App\Models\IPO;
use Illuminate\Support\Facades\Storage;

class ApiController extends BaseController
{
    public function noti_test()
    {
        $notificationSendData = [
                'notification_title' => "hello",
                'notification_description' => "Test1",
                'notification_image' => "https://images.pexels.com/photos/674010/pexels-photo-674010.jpeg",
            ];
            $send_notification = ApplicationNotification::sendOneSignalNotificationSchedule($notificationSendData);
    }
    
    public function getIPOList(Request $request)
    {
        $status = $request->status; // Filter by status
        $type = $request->type;
        $page = $request->page_no ?? 1;
        $perPage = $request->per_page ?? 10;

        if ($status == "") {
            return $this->sendError("Status field required", 401);
        }
        $query = IPO::query();
        if ($status) {
            $query->where('status', $status);
        }
        if ($type) {
            $query->where('type', $type);
        }
        $result = $query->paginate($perPage, ['*'], 'page', $page);

        $paginationDetails = [
            'total_record' => $result->total(),
            'per_page' => $result->perPage(),
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage(),
        ];
        $responseData['pagination'] = $paginationDetails;
        $responseData['result_data'] = $result->items();

        $response = $this->encryptData($responseData);
        return $this->sendResponse($responseData, 'Data get successfully.');
    }
    
    public function fetchAndStore() # testing purpose
    {
        // \Log::info("Cron: controller started");
        $apiUrl = 'https://api.ipoalerts.in/ipos';
        $apiKey = '94b1af57ba8e33a26572182d933361eb3f70cc0cc33617d1903db6c69d008db1';
        // IPO statuses to fetch
        $statuses = ['open', 'upcoming', 'closed', 'announced', 'listed'];
        foreach ($statuses as $status) {
            // Fetch data for each status
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->get("$apiUrl?status=$status");
            // Check for successful response
            if ($response->failed()) {
                // \Log::error("Failed to fetch data for status: $status");
                continue; // Skip to the next status if this fails
            }
            $data = $response->json();
            // Ensure data['ipos'] exists and is iterable
            if (!isset($data['ipos']) || !is_array($data['ipos'])) {
                // \Log::warning("No IPO data found for status: $status");
                continue;
            }
            // Process and store IPOs
            foreach ($data['ipos'] as $ipoData) {
                IPO::updateOrCreate(
                    ['ipo_id' => $ipoData['id']], // Unique identifier
                    [
                        'status' => array_key_exists('status', $ipoData) ? $ipoData['status'] : null,
                        'slug' => array_key_exists('slug', $ipoData) ? $ipoData['slug'] : null,
                        'info_url' => array_key_exists('infoUrl', $ipoData) ? $ipoData['infoUrl'] : null,
                        'name' => array_key_exists('name', $ipoData) ? $ipoData['name'] : null,
                        'symbol' => array_key_exists('symbol', $ipoData) ? $ipoData['symbol'] : null,
                        'type' => array_key_exists('type', $ipoData) ? $ipoData['type'] : null,
                        'start_date' => array_key_exists('startDate', $ipoData) ? $ipoData['startDate'] : null,
                        'end_date' => array_key_exists('endDate', $ipoData) ? $ipoData['endDate'] : null,
                        'listing_date' => array_key_exists('listingDate', $ipoData) ? $ipoData['listingDate'] : null,
                        'price_range' => array_key_exists('priceRange', $ipoData) ? $ipoData['priceRange'] : null,
                        'min_qty' => array_key_exists('minQty', $ipoData) ? $ipoData['minQty'] : null,
                        'logo' => array_key_exists('logo', $ipoData) ? $ipoData['logo'] : null,
                        'min_amount' => array_key_exists('minAmount', $ipoData) ? $ipoData['minAmount'] : null,
                        'issue_size' => array_key_exists('issueSize', $ipoData) ? $ipoData['issueSize'] : null,
                        'prospectus_url' => array_key_exists('prospectusUrl', $ipoData) ? $ipoData['prospectusUrl'] : null,
                        'schedule' => array_key_exists('schedule', $ipoData) ? json_encode($ipoData['schedule']) : null,
                        // 'schedule' => isset($ipoData['schedule']) ? json_encode($ipoData['schedule']) : null,
                    ]
                );
            }
            \Log::info("Data fetched and stored for status: $status");
        }
        \Log::info("Cron: controller completed");
        return response()->json(['message' => 'IPO data successfully updated']);
    }
    
    public function user_count(Request $request)
    {
        $data = DB::table('common_settings')->where('setting_key','=' ,'app_install_count')->pluck('setting_value')->first();
        // if($data){
            $count = $data + 1;
            DB::table('common_settings')->where('setting_key','=','app_install_count')->update(['setting_value'=>$count]);
        // }
        return $this->sendResponse([], 'Data get Successfully!');
    }
  
    public function get_privacy(Request $request)
    {
        //  \Log::info("privacy.. : ". request()->ip()); 
        try {
            // Define a unique cache key for the privacy policy
            $cacheKey = 'privacy_policy';
    
            // Use cache to store/retrieve privacy policy data. Cache it for 24 hours (1440 minutes).
            $settings = Cache::remember($cacheKey, 1440, function () {
                return DB::table('common_settings')
                    ->where('setting_key', '=', 'privacy_policy')
                    ->pluck('setting_value')
                    ->first();
            });
    
            // Format the settings data
            $formattedSettings['privacy_policy'] = $settings;
    
            // Return the response
            return $this->sendResponse($formattedSettings, 'Data retrieved successfully!');
        } catch (\Exception $e) {
            return $this->sendError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function get_common_setting(Request $request)
    {
        //  \Log::info("common setting..: ". request()->ip()); 
        // Cache the settings to reduce DB hits
        
        $cacheKey = 'common_settings';
        // Cache for 60 minutes (or adjust as needed)
        $settings = Cache::remember($cacheKey, 1440, function() {
            return DB::table('common_settings')->where('setting_key','!=','privacy_policy')->get();
        });
        
        // $settings = DB::table('common_settings')->where('setting_key','!=','privacy_policy')->get();   // without cache
        $formattedSettings = [];
        
        /*# Coma seperated string 
        foreach ($settings as $setting) {
            $values = explode(',', $setting->setting_value);
            
            // foreach ($values as $value) {
            //     $formattedSettings[$setting->setting_key][] = $value;
            // }
            
            // If there's only one value, set it as a string, otherwise set it as an array
            if (count($values) === 1) {
                $formattedSettings[$setting->setting_key] = $values[0];
            } else {
                $formattedSettings[$setting->setting_key] = $values;
            }
            
            // if($setting->setting_key == "privacy_policy"){
            //     $formattedSettings[$setting->setting_key] = $setting->setting_value;
            // }
        }*/
        
        # array to get data key wise
        foreach ($settings as $setting) {
            // Decode the JSON-like string into an array
            $decodedValue = json_decode($setting->setting_value, true);
            $formattedSettings[$setting->setting_key] = is_array($decodedValue) ? $decodedValue : $setting->setting_value;
        }
        
        $response = $this->encryptData($formattedSettings);
        return $this->sendResponse($response, 'Data get Successfully!');
    }
    
    public function user_login_old(Request $request)
    {
        try {
            $request->validate(['device_token' => 'required|string|max:255']);
            $user = User::where('device_token', $request->device_token)->first();
            if ($user) {
                $token = $this->generateRandomToken();
                $user->update(['remember_token' => $token]);

                $response = $this->encryptData($user);
                return $this->sendResponse($response, 'User login successfully.');
            } else {
                $input = $request->only('device_token');
                $input['remember_token'] = $this->generateRandomToken();
                $user = User::create($input);

                $response = $this->encryptData($user);
                return $this->sendResponse($response, 'User sign-up successfully.');
            }
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->first();
            return $this->sendError($errors);
        } catch (\Exception $e) {
            return $this->sendError('An unexpected error occurred: ' . $e->getMessage());
        }
    }
    
    public function user_login(Request $request)   // static 
    {
        // \Log::info("login..: ". request()->ip()); 
        // \Log::info($request->device_token);
        try {
            $user = ["device_token" => "abc",
                    "notification_status"=> 0,
                     "remember_token" => "78df7081eeddf1ebf05aae20b1ef7d3fa33d6831c97c3654284c7cffd951245b",
                    "updated_at" => "2024-10-17T05:24:33.000000Z",
                    "created_at" => "2024-10-17T05:24:33.000000Z",
                    "id"=> 2 ];
                    //  \Log::info($user);
                $response = $this->encryptData($user);
                //  \Log::info($response);
                return $this->sendResponse($response, 'User sign-up successfully.');
        } catch (\Exception $e) {
            // \Log::info("fail login..");
            return $this->sendError('An unexpected error occurred: ' . $e->getMessage());
        }
    }
    
    public function get_article(Request $request)
    {
        //   Cache::flush();
        $todayDate = date('d M, Y');
        $perPage = 8;
        $page = !empty($request->page_no) ? $request->page_no : 1;

        $cacheKey = 'articles_page_' . $page;
        $cachedArticles = Cache::remember($cacheKey, 1440, function () {
            $response = Http::withOptions([
                'CURLOPT_SSLVERSION' => CURL_SSLVERSION_TLSv1_2,
            ])->get('https://apps.videoapps.club/common_news/new_list_articles.php');
    
            if ($response->failed()) {
                return []; 
            }
            return $response->json();
        });
        if (empty($cachedArticles) || !isset($cachedArticles['articles'])) {
            return $this->sendResponse([], 'No articles found.', 404);
        }
        $getData = $cachedArticles['articles'];
        $getArticles = array_slice($getData, ($page - 1) * $perPage, $perPage);

        $latestArticles = [];
        $blank_image = asset('storage/app/public/news_images/placholder.jpg');
        foreach ($getArticles as $key => $article) {
            $publishedAt = $article['published_at'] ?? null;
            $img_url = $article['image_url'] ?? $blank_image;

            if ($publishedAt && strtotime($publishedAt)) {
                $content = $article['content'];
                $first_sentence = preg_split('/[.|\r]/', $content)[0] ?? '';

                $latestArticles[] = [
                    'title' => $article['title'] ?? 'Data not found',
                    'description' => $article['description'] ?? 'Data not found',
                    'content' => $first_sentence,
                    'author' => $article['author'],
                    'url' => $img_url,
                    'publishedAt' => $todayDate,
                    'detail_url' => $article['url'],
                    'source_name' => $article['source_name'],
                ];
            }
        }

        $last = count($getData) / $perPage;
        $paginationDetails = [
            'total_record' => count($getData),
            'per_page' => (int) $perPage,
            'current_page' => (int) $page,
            'last_page' => ceil($last),
        ];

        $responseData['pagination'] = $paginationDetails;
        $responseData['article_data'] = $latestArticles;
        $response = $this->encryptData($responseData);

        return $this->sendResponse($response, 'Data retrieved successfully.');
    }

    public function get_today_prices(Request $request)
    {
        // Cache::flush();
        try {
            $cacheKey = 'today_prices';
            $data = Cache::remember($cacheKey, 1440, function () {
                return DB::table('common_prices')
                    ->join('states', 'common_prices.state_id', '=', 'states.id')
                    ->select('common_prices.*', 'states.state_name as state_name')
                    ->get();

            });
            if ($data->isNotEmpty()) {
                $response = $this->encryptData($data);
                return $this->sendResponse($data, 'Data retrieved successfully.');
            } else {
                return $this->sendError("Oops! Data not found.");
            }
        } catch (\Exception $e) {
            return $this->sendError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function get_today_prices_city_wise(Request $request)
    {
        // Cache::flush();
        try {
            $request->validate(['city_id' => 'required']);
            $city_id = $request->city_id;

            $cacheKey = 'today_prices_city_wise' . $city_id;
            $data = Cache::remember($cacheKey, 1440, function () use($city_id) {
                return DB::table('common_prices as cp')
                ->join('states', 'cp.state_id', '=', 'states.id')
                ->join('price_cities', 'states.id', '=', 'price_cities.state_id')
                ->select('cp.id',
                    'cp.petrol','cp.diesel','cp.cng','cp.lpg',
                    'price_cities.city_name as city_name'
                )
                ->where('price_cities.id', $city_id)
                ->get();
            });
    
            if ($data->isNotEmpty()) {
                $response = $this->encryptData($data);
                return $this->sendResponse($data, 'Data retrieved successfully.');
            } else {
                return $this->sendError("Oops! Data not found.");
            }
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->first();
            return $this->sendError($errors);
        } catch (\Exception $e) {
            return $this->sendError('An unexpected error occurred: ' . $e->getMessage());
        }         
    }
    
    public function get_price_city_list(Request $request)
    {
        // Cache::flush();
        try {
            $cacheKey = 'price_city_list';
            $city_data = Cache::remember($cacheKey, 1440, function () {
                return DB::table('price_cities')
                ->join('states', 'price_cities.state_id', '=', 'states.id')
                ->select('price_cities.id', 'price_cities.city_name', 'states.state_name')
                ->get();
            });
            $default_detail = Cache::remember("default_detail", 1440, function () {
                return DB::table('common_prices as cp')
                ->join('states', 'cp.state_id', '=', 'states.id')
                ->join('price_cities', 'states.id', '=', 'price_cities.state_id')
                ->select('cp.id',
                    'cp.petrol','cp.diesel','cp.cng','cp.lpg',
                    'price_cities.city_name as city_name')
                ->where('price_cities.id',361) # default mumbai data
                ->first();
            });      
            if ($city_data->isNotEmpty()) {
                $responseData['default_price_detail'] = $default_detail;
                $responseData['city_list'] = $city_data;

                $response = $this->encryptData($responseData);
                return $this->sendResponse($response, 'City list retrieved successfully.');
            } else {
                return $this->sendError("Oops! Data not found.");
            }
        } catch (\Exception $e) {
            return $this->sendError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function get_city_details(Request $request)
    {
        $city_id = $request->id;
        if($city_id == ""){
            return $this->sendError("city id is required.");
        }
        try {
             $data = DB::table('cities')
            ->join('states', 'cities.state_id', '=', 'states.id') // Join with states based on state_id
            ->where('cities.id', $city_id) // Filter by city_id
            ->select('cities.*', 'states.website_link') // Select city data and website_link from states
            ->first();
            if ($data) {

                $response = $this->encryptData($data);
                return $this->sendResponse($response, 'Data retrieved successfully.');
            } else {
                return $this->sendError("Oops! Data not found.");
            }
        } catch (\Exception $e) {
            return $this->sendError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    ################################################################################# testings
    public function noti_test_artic()
    {
        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('H:i');

        $articles = DB::connection('mysql2')
            ->table('articles')
            ->select('title', 'image_url', 'today_date')
            ->orderBy('today_date', 'desc')
            ->take(3)
            ->get();

        if ($articles->isEmpty()) {
            return;
        }

        if ($currentTime >= '00:00' && $currentTime < '09:00') {
            $article = $articles[0]; // First article (9 AM)
        } elseif ($currentTime >= '18:00' && $currentTime < '21:00') {
            $article = $articles[1]; // Second article (9 PM)
        } else {
            $article = $articles[2]; // Default case
        }

        if (!is_null($article)) {
            $imageUrl = empty($article->image_url) ? asset('img/No-Image-Placeholder.svg') : 'https://apps.videoapps.club/common_news/' . $article->image_url;

            $notificationSendData = [
                'notification_title' => $article->title,
                'notification_description' => $article->title,
                'notification_image' => $imageUrl,
            ];
            $send_notification = ApplicationNotification::sendOneSignalNotificationSchedule($notificationSendData);
        }
    }

    public function test_noti()  # ONE SIGNAL NOTIFICATION
    {
        $response = Http::get('https://apps.videoapps.club/common_news/list_articles.php');
        $articles = $response->json();

        date_default_timezone_set('Asia/Kolkata');
        $currentTime = date('H:i');
        if ($currentTime >= '00:00' && $currentTime < '09:00') {
            $article = $articles['articles'][0];   # 9 AM
        } elseif ($currentTime >= '18:00' && $currentTime < '21:00') {
            $article = $articles['articles'][1];   # 9 PM
        }else {
            $article = $articles['articles'][2];
        }

        if (!is_null($article)) {

            $imageUrl = ($article['image_url'] == null) ? asset('img/No-Image-Placeholder.svg') : asset($article['image_url']);
            $notificationSendData['notification_title'] = $article['title'];
            $notificationSendData['notification_description'] = $article['title'];
            $notificationSendData['notification_image'] = $imageUrl;
            $send_notification = ApplicationNotification::sendOneSignalNotificationSchedule($notificationSendData);
            // \Log::info("Cron: article notification sent");
        }
    }

    public function store_city()
    {
        $json = Storage::get('districts.json');
        $data = json_decode($json, true);

        // dd($data['districts']);
        foreach ($data['districts'] as $city) {

            // $state = DB::table('states')->where('state_name', $city['state'])->first();
            // if ($state) {

            if($city['state'] == "National Capital Territory of Delhi") {
                DB::table('price_cities')->insert([
                    'city_name' => $city['district'],
                    'state_id' => 10
                ]);
            }
            // else{
            //     \Log::info("state not found..: " .$city['state']); 
            // }
        }
    }

    /*public function store_city()
    {
        $json = Storage::get('cities.json');
        $cities = json_decode($json, true);

        foreach ($cities as $city) {
            $state = DB::table('states')->where('state_name', $city['state'])->first();

            if ($state) {
                DB::table('new_cities')->insert([
                    'city_name' => $city['name'],
                    'state_id' => $state->id
                ]);
            }
        }
    } */

}
