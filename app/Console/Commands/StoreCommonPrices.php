<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StoreCommonPrices extends Command
{
    protected $signature = 'store:commonprices';
    protected $description = 'Fetch and store common prices data';

    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function handle()
    {
         Cache::flush(); 

        # FUEL PRICE DATA STORE
        $responseFuel = Http::withHeaders([
            'x-rapidapi-host' => 'daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com',
            'x-rapidapi-key' => '1946e7ab0cmshd78f68cac3c8f36p1a3f40jsne73ffa3b085d'
        ])->get('https://daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com/v1/fuel-prices/today/india/states');

        $fuelData = $responseFuel->json();

        if (isset($fuelData['message']))  # ANOTHER API KEY USED
        {
            $res1 = Http::withHeaders([
                'x-rapidapi-host' => 'daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com',
                'x-rapidapi-key' => 'fab1823340msh62c461232111947p13ef58jsn7edddd982862'
            ])->get('https://daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com/v1/fuel-prices/today/india/states');
            $fuelData = $res1->json();
        }
        if (isset($fuelData['message']))  # ANOTHER API KEY USED
        {
            $res1 = Http::withHeaders([
                'x-rapidapi-host' => 'daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com',
                'x-rapidapi-key' => '2dd38dbaf3mshd608728c8e1956fp1a2409jsnc545b958625c'
            ])->get('https://daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com/v1/fuel-prices/today/india/states');
            $fuelData = $res1->json();
        }

        if (isset($fuelData['statePrices']) && is_array($fuelData['statePrices'])) {
            foreach ($fuelData['statePrices'] as $value) {

                $stateName = $value['stateName'];
                $state_id = DB::table('states')->where('state_name', 'like', '%' . $stateName . '%')->pluck('id')->first();
                if ($state_id) {

                    $petrol = isset($value['fuel']['petrol']['retailPrice']) ? $value['fuel']['petrol']['retailPrice'] : null;
                    $diesel = isset($value['fuel']['diesel']['retailPrice']) ? $value['fuel']['diesel']['retailPrice'] : null;
                    $cng    = isset($value['fuel']['cng']['retailPrice']) ? $value['fuel']['cng']['retailPrice'] : null;
                    $lpg    = isset($value['fuel']['lpg']['retailPrice']) ? $value['fuel']['lpg']['retailPrice'] : null;


                    // Only add non-null values to the update array
                    if ($petrol !== null) {
                        $fuel_data_store['petrol'] = $petrol;
                    }
                    if ($diesel !== null) {
                        $fuel_data_store['diesel'] = $diesel;
                    }
                    // if ($cng !== null) {
                    //     $fuel_data_store['cng'] = $cng;
                    // }
                    if ($lpg !== null) {
                        $fuel_data_store['lpg'] = $lpg;
                    }

                    $fuel_data_store['today_date'] = date('Y-m-d H:i:s');
                    $fuel_data_store['state_id'] = $state_id;

                    $existingRecord = DB::table('common_prices')->where('state_id', $state_id)->first();
                    if ($existingRecord) {
                        DB::table('common_prices')->where('state_id', $state_id)->update($fuel_data_store);
                    } else {
                        DB::table('common_prices')->insert($fuel_data_store);
                    }
                }
            }
            // $data = DB::table('common_prices')->whereDate('today_date','!=', $todayDate)->delete();
        }
    }


    public function handle__()
    {
        // \Log::info("Cron: before price");
        
        # GOLD PRICE DATA STORE MONTHLY LIMITED REQUEST ONLY
        // $response = Http::withHeaders([
        //     'x-rapidapi-host' => 'gold-rates-india.p.rapidapi.com',
        //     'x-rapidapi-key' => '1946e7ab0cmshd78f68cac3c8f36p1a3f40jsne73ffa3b085d'  // je
        // ])->get('https://gold-rates-india.p.rapidapi.com/api/state-gold-rates');
        // $data = $response->json();

        // if (isset($data['message']))  # ANOTHER API KEY USED
        // {
        //     $res = Http::withHeaders([
        //         'x-rapidapi-host' => 'gold-rates-india.p.rapidapi.com',
        //         'x-rapidapi-key' => 'fab1823340msh62c461232111947p13ef58jsn7edddd982862' // ki
        //     ])->get('https://gold-rates-india.p.rapidapi.com/api/state-gold-rates');
        //     $data = $res->json();
        // }
        // if (isset($data['message']))  # ANOTHER API KEY USED
        // {
        //     $res = Http::withHeaders([
        //         'x-rapidapi-host' => 'gold-rates-india.p.rapidapi.com',
        //         'x-rapidapi-key' => '2dd38dbaf3mshd608728c8e1956fp1a2409jsnc545b958625c'  // rk
        //     ])->get('https://gold-rates-india.p.rapidapi.com/api/state-gold-rates');
        //     $data = $res->json();
        // }

        // if (isset($data['GoldRate']) && is_array($data['GoldRate'])) {
        //     foreach ($data['GoldRate'] as $rate) {

        //         $stateName = str_replace('-', ' ', $rate['state']);  # REMOVE "-" FROM NAME
        //         $state_id = DB::table('states')->where('state_name', 'like', '%' . $stateName . '%')->pluck('id')->first();
        //         if ($state_id) {
        //             $gold_22k = $rate['TenGram22K'];
        //             $gold_24k = $rate['TenGram24K'];

        //             DB::table('common_prices')->insert(
        //                 ['state_id' => $state_id,
        //                 'gold_22k' => $gold_22k,
        //                 'gold_24k' => $gold_24k,
        //                 ]
        //             );
        //         }
        //     }
        // }

        # FUEL PRICE DATA STORE
        $responseFuel = Http::withHeaders([
            'x-rapidapi-host' => 'daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com',
            'x-rapidapi-key' => '1946e7ab0cmshd78f68cac3c8f36p1a3f40jsne73ffa3b085d'
        ])->get('https://daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com/v1/fuel-prices/today/india/states');

        $fuelData = $responseFuel->json();

        if (isset($fuelData['message']))  # ANOTHER API KEY USED
        {
            $res1 = Http::withHeaders([
                'x-rapidapi-host' => 'daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com',
                'x-rapidapi-key' => 'fab1823340msh62c461232111947p13ef58jsn7edddd982862'
            ])->get('https://daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com/v1/fuel-prices/today/india/states');
            $fuelData = $res1->json();
        }
        if (isset($fuelData['message']))  # ANOTHER API KEY USED
        {
            $res1 = Http::withHeaders([
                'x-rapidapi-host' => 'daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com',
                'x-rapidapi-key' => '2dd38dbaf3mshd608728c8e1956fp1a2409jsnc545b958625c'
            ])->get('https://daily-petrol-diesel-lpg-cng-fuel-prices-in-india.p.rapidapi.com/v1/fuel-prices/today/india/states');
            $fuelData = $res1->json();
        }

        if (isset($fuelData['statePrices']) && is_array($fuelData['statePrices'])) {
            foreach ($fuelData['statePrices'] as $value) {

                $stateName = $value['stateName'];
                $state_id = DB::table('states')->where('state_name', 'like', '%' . $stateName . '%')->pluck('id')->first();
                if ($state_id) {

                    $petrol = isset($value['fuel']['petrol']['retailPrice']) ? $value['fuel']['petrol']['retailPrice'] : null;
                    $diesel = isset($value['fuel']['diesel']['retailPrice']) ? $value['fuel']['diesel']['retailPrice'] : null;
                    $cng    = isset($value['fuel']['cng']['retailPrice']) ? $value['fuel']['cng']['retailPrice'] : null;
                    $lpg    = isset($value['fuel']['lpg']['retailPrice']) ? $value['fuel']['lpg']['retailPrice'] : null;


                    // Only add non-null values to the update array
                    if ($petrol !== null) {
                        $fuel_data_store['petrol'] = $petrol;
                    }
                    if ($diesel !== null) {
                        $fuel_data_store['diesel'] = $diesel;
                    }
                    if ($cng !== null) {
                        $fuel_data_store['cng'] = $cng;
                    }
                    if ($lpg !== null) {
                        $fuel_data_store['lpg'] = $lpg;
                    }
                    
                     $fuel_data_store['today_date'] = date('Y-m-d H:i:s');

                    $existingRecord = DB::table('common_prices')->where('state_id', $state_id)->first();
                    if ($existingRecord) {
                        DB::table('common_prices')->where('state_id', $state_id)->update($fuel_data_store);
                    } else {
                        DB::table('common_prices')->insert($fuel_data_store);
                    }
                }
            }
            // $data = DB::table('common_prices')->whereDate('today_date','!=', $todayDate)->delete();
        }
    
        // \Log::info("Cron: after price");
    }


}
