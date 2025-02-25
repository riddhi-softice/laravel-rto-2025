<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController as BaseController;
use App\Models\UrlConfig;
use Illuminate\Http\Request;
use DB;
use App\Helpers\EncryptionHelper;

class UrlController extends BaseController
{
    public function trackurl(Request $request)
    {
        $url_type = $request->url_type;
        if($url_type == ""){
            return "url type required!";
        }
        $url_brand_id =  DB::table('url_brands')->where('name',$url_type)->pluck('id')->first();
        $url_data   =  DB::table('url_config')->where('url_brand_id',$url_brand_id)->first();
        if(!$url_data){
            return "Something went wrong!";
        }
        $fix_params = DB::table('url_dynamic_params')->where('url_id',$url_data->id)->where('value_status','fixed')->pluck('param_value','param_key')->toArray();
        # random value change
        foreach ($fix_params as $key2 => $value2) {  
            if (isset($value2) && strpos($value2, 'random_id') !== false) {
                $randomNumber = time().rand(1000, 9999);  
                $fix_params[$key2] = str_replace('random_id', $randomNumber, $value2);
            }
        }
        $final_url = $url_data->base_url .'?';
        $query_params = $request->all();
        unset($query_params['url_type']);

       $user_unique = time().'_'.rand(10000, 99999);  
        if(count($query_params) > 0){
            foreach ($query_params as $key => $value) { 
                if(!empty($value)){
                    DB::table('url_logs')->insert([
                        'url_brand_id' => $url_brand_id,
                        'url_id' => $url_data->id,
                        'params_key' => $key,
                        'param_value' => $value,
                         'url_counter' => $user_unique,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }else {
             DB::table('url_logs')->insert([
                        'url_brand_id' => $url_brand_id,
                        'url_id' => $url_data->id,
                         'url_counter' => $user_unique,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
        }

        if (stripos($url_type, "icici") !== false || stripos($url_type, "lombard") !== false) {
            
            $all_params = array_merge($fix_params ,$query_params);
            return view('track_url',['all_params'=> $all_params]);
            
            // Generate AES key/IV
            // $keyIv = EncryptionHelper::generateKeyIv();
            // Encrypt the payload using AES
            // $encryptedPayload = EncryptionHelper::encryptPayload(json_encode($all_params), $keyIv);
            // $payloadEncoded = EncryptionHelper::replaceSpecialChars($encryptedPayload);
            // // Encrypt AES Key/IV using RSA
            // $encryptedKeyIv = EncryptionHelper::encryptAesKeyIv($keyIv);
            // $keyEncoded = EncryptionHelper::replaceSpecialChars($encryptedKeyIv);
            // Generate final URL
            // $final_url = $final_url ."d=" . $payloadEncoded . "&k=" . $keyEncoded;
        }else{
            $fix_query_string = http_build_query($fix_params);
            if(!empty($fix_query_string)){
                $final_url = $final_url . $fix_query_string .'&';
            }
            
            $query_string = http_build_query($query_params);
            if(!empty($query_string)){
                $final_url = $final_url .$query_string;
            }
        }

        // \Log::info("redirect URL.. : ". $final_url);
        return redirect($final_url);
    }

    # with value status    
    public function trackurl_old(Request $request)
    {
        $url_type = $request->url_type;
        if($url_type == ""){
            return "url type required!";
        }
        $url_brand_id =  DB::table('url_brands')->where('name',$url_type)->pluck('id')->first();
        $url_data   =  DB::table('url_config')->where('url_brand_id',$url_brand_id)->first();
        if(!$url_data){
            return "Something went wrong!";
        }

        /*$missing_keys = [];
        foreach ($param_keys as $key) {
            if (!$request->has($key)) {
                $missing_keys[] = $key;
            }
        }
        if (!empty($missing_keys)) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => array_map(fn($key) => "The $key field is required.", $missing_keys),
            ], 422);
        }*/

        $fix_params = DB::table('url_dynamic_params')->where('url_id',$url_data->id)->whereNotNull('param_value')->pluck('param_value','param_key')->toArray();
        # random value change
        foreach ($fix_params as $key2 => $value2) {  
            if (isset($value2) && strpos($value2, 'random_id') !== false) {
                $randomNumber = time().rand(1000, 9999);  
                $fix_params[$key2] = str_replace('random_id', $randomNumber, $value2);
            }
        }
        $fix_query_string = http_build_query($fix_params);
        $final_url = $url_data->base_url . '?' .$fix_query_string;
        $query_params = $request->all();
        unset($query_params['url_type']);

        $query_string = http_build_query($query_params);
        if(!empty($query_string)){
            $final_url = $final_url . '&' . $query_string;
        }

        DB::table('url_logs')->insert([
            'url_brand_id' => $url_brand_id,
            'url_id' => $url_data->id,
            'query_params' => json_encode($query_params),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect($final_url);
    }

    # type wise
    /*public function trackurl_old(Request $request)
    {
        $url_type = $request->url_type;
        if($url_type == ""){
            return "url type required!";
        }

        $url_data =  DB::table('url_config')->where('url_type',$url_type)->first();
        $param_keys = DB::table('url_dynamic_params')->where('url_id',$url_data->id)->whereNull('param_value')->pluck('param_key')->toArray();
        // dd($param_keys);

        $missing_keys = [];
        foreach ($param_keys as $key) {
            if (!$request->has($key)) {
                $missing_keys[] = $key;
            }
        }
        if (!empty($missing_keys)) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => array_map(fn($key) => "The $key field is required.", $missing_keys),
            ], 422);
        }

        $fix_params = DB::table('url_dynamic_params')->where('url_id',$url_data->id)->whereNotNull('param_value')->pluck('param_value','param_key')->toArray();
        # random value change
        foreach ($fix_params as $key2 => $value2) {  
            if (isset($value2) && strpos($value2, 'random_id') !== false) {
                $randomNumber = time().rand(1000, 9999);  
                $fix_params[$key2] = str_replace('random_id', $randomNumber, $value2);
            }
        }
        $fix_query_string = http_build_query($fix_params);
        $base_url = $url_data->base_url . '?' .$fix_query_string;
        $query_params = $request->all();
        unset($query_params['url_type']);

        $query_string = http_build_query($query_params);
        $final_url = $base_url . '&' . $query_string;

        DB::table('url_logs')->insert([
            'url_type' => $url_type,
            'base_url' => $base_url,
            'query_params' => json_encode($query_params),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect($final_url);
        
        // return response()->json([
        //     'return_url' => $final_url,
        // ]);
    }*/
}

