<?php

namespace App\Http\Controllers;


// use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VehicleController extends Controller
{
    public function getVehicleInfo(Request $request)
    {
        $vehicleId = $request->vehicleId;
        if($vehicleId == ""){
            return response()->json([
                'error' => 'VehicleId required',
                'status' => $response->status(),
            ], 500);
        }
        $apiUrl = 'https://prod.apiclub.in/api/v1/rc_info';
        $apiKey = 'apclb_TfacRQTvo9foquFjB8gsYGDIc7e6daeb';

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'accept' => 'application/json',
            'Referer' => 'prod.apiclub.in',
        ])->asForm()->post($apiUrl, [
            'vehicleId' => $vehicleId,
        ]);
        
        // if ($response->failed()) {
        //     return response()->json([
        //         'error' => 'Failed to fetch data from the API',
        //         'status' => $response->status(),
        //     ], 200);
        // }
        
        // $encryptedResponse = $this->encryptData($response);
    
        return $response;
    }

    public function getChallanInfo(Request $request)
    {
        $apiUrl = 'https://prod.apiclub.in/api/v1/challan_info_v2';
        $apiKey = 'apclb_TfacRQTvo9foquFjB8gsYGDIc7e6daeb';

        $formData = [
            'vehicleId' => $request->vehicleId,
            'chassis' => $request->chassis,
            'engine_no' => $request->engine_no,
        ];
        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'accept' => 'application/json',
            'Referer' => 'prod.apiclub.in',
        ])->asForm()->post($apiUrl, $formData);
        
        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to fetch data from the API',
                'status' => $response->status(),
            ], 500);
        }
        
        // $encryptedResponse = $this->encryptData($response->json());
    
        return response()->json($response->json());
    }

    public function fetchDLInfo(Request $request)
    {
        $apiUrl = 'https://prod.apiclub.in/api/v1/fetch_dl';
        $apiKey = 'apclb_TfacRQTvo9foquFjB8gsYGDIc7e6daeb';

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'accept' => 'application/json',
            'Referer' => 'prod.apiclub.in',
        ])->asForm()->post($apiUrl, [
            'dl_no' => $request->dl_no,
            'dob' => $request->dob,
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to fetch data from the API',
                'status' => $response->status(),
                'body' => $response->body(),
            ], 500);
        }
        
         // $encryptedResponse = $this->encryptData($response->json());
        return response()->json($response->json());
    }



}
