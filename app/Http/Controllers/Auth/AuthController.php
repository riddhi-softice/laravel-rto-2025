<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use DB;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function postLogin(Request $request)
    {
        $data = Admin::where('email', $request->email)->first();
        if (!is_null($data)) {
            if (!Hash::check($request->password, $data->password)) {
                return redirect('/login')->with('error', 'Oppes! You have entered Incorrect password.');
            }
            session([
                'admin_id' => $data->id,
                'admin_name' => $data->name,
            ]);
            return redirect()->intended('dashboard')->withSuccess('You have Successfully loggedin');
        }
        return redirect('/login')->with('error', 'Oppes! You have entered invalid credentials.');
    }

    public function dashboard()
    {
        if (Session::has('admin_name') && !empty(session('admin_name'))) 
        {
            /*$type_array = DB::table('url_logs')
            ->select(
                'url_logs.url_brand_id',
                'url_brands.name',
                DB::raw('COUNT(url_logs.id) as user_count')
            )
            ->join('url_brands', 'url_logs.url_brand_id', '=', 'url_brands.id')
            ->groupBy('url_logs.url_brand_id', 'url_brands.name')
            ->get();*/
            // $data['type_array'] = $type_array;

            $data = DB::table('url_logs')->whereNotNull('url_counter')->distinct('url_counter')->count();
            
            return view('dashboard',['data' => $data]);
        }
        return redirect('/login')->with('error', 'Opps! You do not have access.');
    }

    public function dashboard_old()
    {
        if (Session::has('admin_name') && !empty(session('admin_name'))) 
        {
            $type_array = DB::table('url_logs')->select('url_type', DB::raw('count(id) as user_count'))->groupBy('url_type')->get();
            $keyCounts = [];
            $logs = DB::table('url_logs')->select('query_params')->get();
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
            $data['keyCounts'] = $keyCounts;
            $data['type_array'] = $type_array;
            return view('dashboard',['data' => $data]);
        }
        return redirect('/login')->with('error', 'Opps! You do not have access.');
    }

    public function account_setting(Request $request)
    {
        $data = Admin::where('id', session('admin_id'))->first();
        return view('auth.account_setting', compact('data'));
    }

    public function account_setting_change(Request $request)
    {
        $data = Admin::where('id',$request->id)->first();
        if (!is_null($data)) {

            if (!Hash::check($request->old_password, $data->password)) {
                return redirect('/account_setting')->with('error', 'The previous password entered does not match the current one.');
            }
            $data->update(['password' => Hash::make($request->password)]);

            return redirect('/login')->with('success', 'The password has been changed successfully.');
        }
        return redirect('/dashboard')->with('error','Opps! Somthing wents wrong');
    }

    public function logout() {
        Session::flush();
        return Redirect('login');
    }

}
