<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UrlConfig;
use App\Models\DynamicParam;
use App\Models\UrlBrand;
use DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables; 
use Carbon\Carbon;

class UrlConfigController extends Controller
{
    public function url_track_index(Request $request)
    {
        $brands = UrlBrand::all(); 
        $all_keys = DB::table('url_logs')->select('params_key')->distinct()->pluck('params_key');
        return view('url_brand.url_track_index', compact('brands','all_keys'));
    }

    public function url_track_ajax(Request $request)
    {
        // dd($request->has('url_brand_id') && !empty($request->url_brand_id));
        if ($request->ajax()) {
            $query = DB::table('url_logs')
                ->select('url_logs.*', 'url_brands.name as brand_name')
                ->leftJoin('url_brands', 'url_logs.url_brand_id', '=', 'url_brands.id');

            if ($request->has('url_brand_id') && !empty($request->url_brand_id)) {
                $query->where('url_logs.url_brand_id', $request->url_brand_id);
            }
            if ($request->filled('params_key')) {
                $query->where('url_logs.params_key', $request->params_key);
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                // $query->whereBetween('url_logs.created_at', [$request->start_date, $request->end_date]);
                $query->whereBetween('url_logs.created_at', [
                    $request->start_date . ' 00:00:00', 
                    $request->end_date . ' 23:59:59'
                ]);
            } elseif ($request->filled('start_date')) {
                $query->whereDate('url_logs.created_at', $request->start_date);
                // $query->whereDate('url_logs.created_at', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->whereDate('url_logs.created_at', '<=', $request->end_date);
            }

            $data = $query->get();
            return DataTables::of($data)
                ->addIndexColumn() 
                ->addColumn('brand_name', function($row){
                    return $row->brand_name;
                })
                ->addColumn('created_at', function($row){
                    return Carbon::parse($row->created_at)->format('d M Y');
                })
                ->rawColumns(['created_at','brand_name'])
                ->make(true); 
        }
        return view('url_brand.url_track_index');
    }
    
    public function track_details($id)
    {
        $keyCounts = [];
        $logs = DB::table('url_logs')->where('url_brand_id',$id)->select('id','query_params')->get();
        foreach ($logs as $log) {
            $params = json_decode($log->query_params, true);
            if (is_array($params)) {
                foreach ($params as $key => $value) {
                    if (!isset($keyCounts[$key])) {
                        $keyCounts[$key] = 0;
                    }
                    $keyCounts[$key]++;
                }
            }
        }  
        $data['url_brand_id'] = $id;
        $data['keyCounts'] = $keyCounts;
        return view('url_brand.url_key_list', compact('data'));
    }

    public function key_track_ajax(Request $request, $url_brand_id,$key)
    {
        if ($request->ajax()) {
            $logs = DB::table('url_logs')->where('url_brand_id',$url_brand_id)->select('id', 'query_params','created_at')->get();

            $filteredLogs = $logs->filter(function ($log) use ($key) {
                $queryParams = json_decode($log->query_params, true);
                return is_array($queryParams) && array_key_exists($key, $queryParams);
            });

            return DataTables::of($filteredLogs)
                ->addIndexColumn() 
                ->addColumn('created_at', function($row){
                    return Carbon::parse($row->created_at)->format('d M Y');
                })
                ->addColumn('query_value', function ($log) use ($key) {
                    $queryParams = json_decode($log->query_params, true);
                    return $queryParams[$key] ?? 'N/A';
                })
                ->rawColumns(['created_at'])
                ->make(true); 
        }
        return view('url_brand.ajax_key_index', ['url_brand_id'=>$url_brand_id,'key' => $key]);
    }

    public function key_track($key)
    {
        $logs = DB::table('url_logs')->select('id', 'query_params')->get();
        $filteredLogs = [];
        foreach ($logs as $log) {
            $log->query_params = json_decode($log->query_params, true);
            if (is_array($log->query_params) && array_key_exists($key, $log->query_params)) {
                $filteredLogs[] = $log;
            }
        }
        return view('url_brand.key_index', ['logs' => $filteredLogs, 'key' => $key]);
    }

    public function index()
    {
        // $static_base_url = "http://localhost/new_project_rto/api/trackurl";  # Local
        // $static_base_url = "https://rtovehicleinfo.com/new_project/api/trackurl";  # Live
        # with null value
        /* $url_configs = DB::table('url_config as uc')
        ->leftJoin('url_dynamic_params as udp', 'uc.id', '=', 'udp.url_id')
        ->selectRaw("
            uc.id AS url_id,
            uc.base_url,
            uc.url_type,
            GROUP_CONCAT(CONCAT(udp.param_key, '=', IFNULL(udp.param_value, '')) SEPARATOR '&') AS query_string,
            CONCAT(uc.base_url, '?', GROUP_CONCAT(CONCAT(udp.param_key, '=', IFNULL(udp.param_value, '')) SEPARATOR '&')) AS original_url,
           
            GROUP_CONCAT(CASE WHEN udp.param_value IS NULL THEN CONCAT(udp.param_key, '=') ELSE NULL END SEPARATOR '&') AS generated_query_string,
            CONCAT('$static_base_url', '?', GROUP_CONCAT(CASE WHEN udp.param_value IS NULL THEN CONCAT(udp.param_key, '=') ELSE NULL END SEPARATOR '&')) AS generate_url
        ")
        ->groupBy('uc.id', 'uc.base_url', 'uc.url_type')
        ->get(); */

        # replace null value with {value} and all params
        /* $url_configs = DB::table('url_config as uc')
        ->leftJoin('url_dynamic_params as udp', 'uc.id', '=', 'udp.url_id')
        ->selectRaw("
            uc.id AS url_id,
            uc.base_url,
            uc.url_type,
            GROUP_CONCAT(CONCAT(udp.param_key, '=', IFNULL(udp.param_value, '')) SEPARATOR '&') AS query_string,
            CONCAT(uc.base_url, '?', GROUP_CONCAT(CONCAT(udp.param_key, '=', IFNULL(udp.param_value, '')) SEPARATOR '&')) AS original_url,

             GROUP_CONCAT(CASE 
            WHEN udp.param_value IS NULL THEN CONCAT(udp.param_key, '={', udp.param_key, '}') 
                ELSE CONCAT(udp.param_key, '=', udp.param_value) 
            END SEPARATOR '&') AS generated_query_string,
            CONCAT('$static_base_url', '?', GROUP_CONCAT(CASE 
                WHEN udp.param_value IS NULL THEN CONCAT(udp.param_key, '={', udp.param_key, '}') 
                ELSE CONCAT(udp.param_key, '=', udp.param_value) 
            END SEPARATOR '&')) AS generate_url
            ")
        ->groupBy('uc.id', 'uc.base_url', 'uc.url_type')
        ->get(); */

        /* $url_configs = DB::table('url_config as uc')
        ->leftJoin('url_dynamic_params as udp', 'uc.id', '=', 'udp.url_id')
        ->leftJoin('url_brands as ub', 'uc.url_brand_id', '=', 'ub.id')
        ->selectRaw("
            uc.id AS url_id,
            uc.base_url,
            ub.name AS brand_name,
            uc.generate_url, 
            GROUP_CONCAT(CONCAT(udp.param_key, '=', IFNULL(udp.param_value, '')) SEPARATOR '&') AS query_string,
            CONCAT(uc.base_url, '?', GROUP_CONCAT(CONCAT(udp.param_key, '=', IFNULL(udp.param_value, '')) SEPARATOR '&')) AS original_url
        ")
        ->groupBy('uc.id', 'uc.base_url', 'uc.url_brand_id', 'ub.name', 'uc.generate_url')  
        ->get(); */

        $url_configs = DB::table('url_config as uc')
        ->leftJoin('url_dynamic_params as udp', 'uc.id', '=', 'udp.url_id')
        ->leftJoin('url_brands as ub', 'uc.url_brand_id', '=', 'ub.id')
        ->selectRaw("
            uc.id AS url_id,
            uc.base_url,
            ub.name AS brand_name,
            uc.generate_url, 
            COALESCE(GROUP_CONCAT(CONCAT(udp.param_key, '=', IFNULL(udp.param_value, '')) SEPARATOR '&'), '') AS query_string,
            CASE 
                WHEN COUNT(udp.id) > 0 THEN CONCAT(uc.base_url, '?', COALESCE(GROUP_CONCAT(CONCAT(udp.param_key, '=', IFNULL(udp.param_value, '')) SEPARATOR '&'), ''))
                ELSE uc.base_url
            END AS original_url
        ")
        ->groupBy('uc.id', 'uc.base_url', 'uc.url_brand_id', 'ub.name', 'uc.generate_url')  
        ->get();

        // $url_configs = UrlConfig::orderBy('id','desc')->get();
        return view('urls.index', compact('url_configs'));
    }

    public function create()
    {
        $UrlBrand = UrlBrand::all(); 
        return view('urls.create',compact('UrlBrand'));
    }

    public function store(Request $request)
    {   
        $request->validate([
            // 'url_type' => 'required|unique:url_config,url_type', 
            'url_brand_id' => 'required|unique:url_config,url_brand_id', 
        ]);
        $url_type = UrlBrand::where('id',$request->url_brand_id)->pluck('name')->first();

        $url_dynamic_params = $request->url_dynamic_params; # array get
        $static_base_url = url('/')."/api/trackurl"; 
        $generate_url = $static_base_url .'?url_type=' .$url_type;

        $formatted_params = [];
        if(!is_null($url_dynamic_params)){
            foreach ($url_dynamic_params as $param) {
                if(is_null($param['param_value'])){
                    $key = $param['param_key'];           
                    $value = $param['param_value'] ?? '{' . $key . '}'; # Replace null with {param_key}
                    $formatted_params[] = $key . '=' . $value;
                }else if($param['value_status'] == "dynamic"){
                    $formatted_params[] = $param['param_key'] . '={' . $param['param_value'] . '}';
                }
            }
        }
        $query_string = implode('&', $formatted_params);
        if(!empty($query_string)){
            $generate_url = $generate_url .'&' .$query_string;
        }

        $input['base_url'] = $request->base_url;
        // $input['url_type'] = $request->url_type;
        $input['url_brand_id'] = $request->url_brand_id;
        $input['generate_url'] = $generate_url;
        $UrlConfig = UrlConfig::create($input);
        $url_id = $UrlConfig->id;

        if(!is_null($url_dynamic_params)){
            foreach ($url_dynamic_params as $key2=> $detail) {
                /*if (isset($detail['param_value']) && strpos($detail['param_value'], 'random_id') !== false) {
                    $randomNumber = rand(1000, 9999);  
                    $detail['param_value'] = str_replace('random_id', $randomNumber, $param['param_value']);
                }*/
                $data = ['url_id'=>$url_id,'param_key'=> $detail['param_key'],'param_value'=>$detail['param_value'],'value_status'=>$detail['value_status'],'created_at'=> now(),'updated_at'=>now()];
                DB::table('url_dynamic_params')->insert($data);
            }
        }
        return redirect()->route('url_configs.index')->with('success', 'Data added successfully.');
    }

    public function edit(UrlConfig $UrlConfig)
    {
        $UrlBrand = UrlBrand::all(); 
        $DynamicParam = DynamicParam::where('url_id',$UrlConfig->id)->get();
        return view('urls.edit', compact('UrlConfig','DynamicParam','UrlBrand'));
    }

    public function update(Request $request, UrlConfig $UrlConfig)
    {
        $request->validate([
            'url_brand_id' => [
                'required',
                Rule::unique('url_config', 'url_brand_id')->ignore($UrlConfig->id),
            ],
        ]);
        $url_type = UrlBrand::where('id',$request->url_brand_id)->pluck('name')->first();
        $url_dynamic_params = $request->url_dynamic_params;
        $static_base_url = url('/')."/api/trackurl"; 
        $generate_url = $static_base_url .'?url_type=' .$url_type;
        
        $formatted_params = [];
        if(!is_null($url_dynamic_params)){
            foreach ($url_dynamic_params as $param) {
                if(is_null($param['param_value']) || $param['param_value'] == ""){
                    $key   = $param['param_key'];
                    $value = $param['param_value'] ?? '{' . $key . '}'; # Replace null with {param_key}
                    $formatted_params[] = $key . '=' . $value;
                }else if($param['value_status'] == "dynamic"){
                    $formatted_params[] = $param['param_key'] . '={' . $param['param_value'] . '}';
                }
            }
        }
        $query_string = implode('&', $formatted_params);
        if(!empty($query_string)){
            $generate_url = $generate_url .'&' .$query_string;
        }
        
        $input['base_url'] = $request->base_url;
        $input['url_brand_id'] = $request->url_brand_id;
        $input['generate_url'] = $generate_url ;
        $UrlConfig->update($input);

        $url_id = $UrlConfig->id;
        DynamicParam::where('url_id',$url_id)->delete();
        if(!is_null($url_dynamic_params)){
            foreach ($url_dynamic_params as $key2 => $detail) {
                
                $data = ['url_id'=>$url_id,'param_key'=> $detail['param_key'],'param_value'=>$detail['param_value'],'value_status'=>$detail['value_status'],'created_at'=> now(),'updated_at'=>now()];
                DB::table('url_dynamic_params')->insert($data);
            }
        }
        return redirect()->route('url_configs.index')->with('success', 'Data updated successfully.');
    }

    public function destroy(UrlConfig $UrlConfig)
    {
        DynamicParam::where('url_id',$UrlConfig->id)->delete();
        $UrlConfig->delete();
        return response()->json(['message' => 'Data deleted successfully'], 200);
    }

}

