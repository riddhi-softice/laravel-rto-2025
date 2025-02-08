<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DB;

class BlockIpMiddleware
{
   // public $blockIps = ['192.168.1.6','182.65.76.116'];  # on a LAN or Wi-Fi connection - ipv4 
   
    public function handle(Request $request, Closure $next)
    {
        $ips = DB::table('common_settings')->where('setting_key','admin_ip')->pluck('setting_value')->first();
        $ip_array = explode(',',$ips);

        #  if (!in_array($request->ip(), $this->blockIps)) {    # access from public variable
        if (!in_array($request->ip(), $ip_array)) {             # access from db
            abort(403, "You are restricted to access the site.");
        }
        return $next($request);
    }

}
