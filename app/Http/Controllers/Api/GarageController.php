<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class GarageController extends BaseController
{
    public function manage_garage_info(Request $request)
    {
        try {
            $request->validate([
                'zone_name' => 'required',
                'zone_meter' => 'required',
                'noti_status' => 'required',
                'child_user_id' => 'required',
                'zone_type' =>   'required',
                'zone_lattitude' => 'required',
                'zone_longitude' => 'required',
            ]);
            $user = Auth::user();
            if (!$user) {
                return $this->sendError('Authentication failed! The provided token is invalid, and the specified user could not be located', 401);
            }
            $data = $request->all();
            $data['parent_user_id'] = $user->id;

            if($request->zone_id){
                unset($data['zone_id']);
                // Find existing record or create a new one
                $geoJsonRecord  = SafeZoneModel::where(['id'=> $request->zone_id])->first();
                if($geoJsonRecord){
                    $geoJsonRecord->update($data);
                }
            }else{
                $geoJsonRecord  = SafeZoneModel::create($data);
            }
            $encryptedResponse = $this->encryptData($geoJsonRecord);
            return $this->sendResponse($encryptedResponse, 'User zone added successfully');
        } catch (ValidationException $e) {

           return $this->sendError($e->validator->errors()->first(), 422);
        } catch (\Exception $e) {
            return $this->sendError('An unexpected error occurred: ' . $e->getMessage());
        }
    }
    
}
