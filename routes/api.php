<?php

use Minotaur\User;
use Minotaur\Monitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('monitors', function(Request $request) {
    // If the Content-Type and Accept headers are set to 'application/json', 
    // this will return a JSON structure. This will be cleaned up later.

    $user = $request->user();

    $monitors = Monitor::where('user_id', $user->id)->get();

    // Get monitor results last 1 hour
    foreach($monitors as $monitor)
    {
        switch($monitor->monitor_type)
        {
            case "dns":
                $errors_last_1hour = DB::table('monitor_results')
                    ->select(DB::raw('count(*) as errors'))
                    ->where('monitor_id', '=', $monitor->monitor_id)
                    ->where('monitor_type', '=', 'dns')
                    ->where('monitor_result', '<=', '0')
                    ->whereRAW('UNIX_TIMESTAMP(created_at) > UNIX_TIMESTAMP((DATE_SUB(NOW(),INTERVAL 1 HOUR)))')
                    ->get();
                break;
            case "ping":
                $errors_last_1hour = DB::table('monitor_results')
                    ->select(DB::raw('count(*) as errors'))
                    ->where('monitor_id', '=', $monitor->monitor_id)
                    ->where('monitor_type', '=', 'ping')
                    ->where('monitor_result', '<=', '0')
                    ->whereRAW('UNIX_TIMESTAMP(created_at) > UNIX_TIMESTAMP((DATE_SUB(NOW(),INTERVAL 1 HOUR)))')
                    ->get();
                break;
            case "http":
                $errors_last_1hour = DB::table('monitor_results')
                    ->select(DB::raw('count(*) as errors'))
                    ->where('monitor_id', '=', $monitor->monitor_id)
                    ->where('monitor_type', '=', 'http')
                    ->where('monitor_result', '!=', '200')
                    ->whereRAW('UNIX_TIMESTAMP(created_at) > UNIX_TIMESTAMP((DATE_SUB(NOW(),INTERVAL 1 HOUR)))')
                    ->get();
                break;
        }
        Log::debug($errors_last_1hour);
        $monitor->errors_last_1hour = $errors_last_1hour[0]->errors;
    }

    // Get monitor results last 1 day
    foreach($monitors as $monitor)
    {
        switch($monitor->monitor_type)
        {
            case "dns":
                $errors_last_1day = DB::table('monitor_results')
                    ->select(DB::raw('count(*) as errors'))
                    ->where('monitor_id', '=', $monitor->monitor_id)
                    ->where('monitor_type', '=', 'dns')
                    ->where('monitor_result', '<=', '0')
                    ->whereRAW('UNIX_TIMESTAMP(created_at) > UNIX_TIMESTAMP((DATE_SUB(NOW(),INTERVAL 1 DAY)))')
                    ->get();
                break;
            case "ping":
                $errors_last_1day = DB::table('monitor_results')
                    ->select(DB::raw('count(*) as errors'))
                    ->where('monitor_id', '=', $monitor->monitor_id)
                    ->where('monitor_type', '=', 'ping')
                    ->where('monitor_result', '<=', '0')
                    ->whereRAW('UNIX_TIMESTAMP(created_at) > UNIX_TIMESTAMP((DATE_SUB(NOW(),INTERVAL 1 DAY)))')
                    ->get();
                break;
            case "http":
                $errors_last_1day = DB::table('monitor_results')
                    ->select(DB::raw('count(*) as errors'))
                    ->where('monitor_id', '=', $monitor->monitor_id)
                    ->where('monitor_type', '=', 'http')
                    ->where('monitor_result', '!=', '200')
                    ->whereRAW('UNIX_TIMESTAMP(created_at) > UNIX_TIMESTAMP((DATE_SUB(NOW(),INTERVAL 1 DAY)))')
                    ->get();
                break;
        }
        Log::debug($errors_last_1day);
        $monitor->errors_last_1day = $errors_last_1day[0]->errors;
    }

    // Get monitor results last 1 week
    foreach($monitors as $monitor)
    {
        switch($monitor->monitor_type)
        {
            case "dns":
                $errors_last_1week = DB::table('monitor_results')
                    ->select(DB::raw('count(*) as errors'))
                    ->where('monitor_id', '=', $monitor->monitor_id)
                    ->where('monitor_type', '=', 'dns')
                    ->where('monitor_result', '<=', '0')
                    ->whereRAW('UNIX_TIMESTAMP(created_at) > UNIX_TIMESTAMP((DATE_SUB(NOW(),INTERVAL 1 WEEK)))')
                    ->get();
                break;
            case "ping":
                $errors_last_1week = DB::table('monitor_results')
                    ->select(DB::raw('count(*) as errors'))
                    ->where('monitor_id', '=', $monitor->monitor_id)
                    ->where('monitor_type', '=', 'ping')
                    ->where('monitor_result', '<=', '0')
                    ->whereRAW('UNIX_TIMESTAMP(created_at) > UNIX_TIMESTAMP((DATE_SUB(NOW(),INTERVAL 1 WEEK)))')
                    ->get();
                break;
            case "http":
                $errors_last_1week = DB::table('monitor_results')
                    ->select(DB::raw('count(*) as errors'))
                    ->where('monitor_id', '=', $monitor->monitor_id)
                    ->where('monitor_type', '=', 'http')
                    ->where('monitor_result', '!=', '200')
                    ->whereRAW('UNIX_TIMESTAMP(created_at) > UNIX_TIMESTAMP((DATE_SUB(NOW(),INTERVAL 1 WEEK)))')
                    ->get();
                break;
        }
        Log::debug($errors_last_1week);
        $monitor->errors_last_1week = $errors_last_1week[0]->errors;
    }
    return $monitors;
});

Route::middleware('auth:sanctum')->get('monitors/all', function(Request $request) {
    // If the Content-Type and Accept headers are set to 'application/json', 
    // this will return a JSON structure. This will be cleaned up later.

    return Monitor::all();
});

Route::middleware('auth:sanctum')->post('monitors/create', function(Request $request) {

    $user = $request->user();

    $monitor = new Monitor;
    $monitor->user_id = $user->id;
    $monitor->monitor_id = strtoupper(Str::random(12));
    $monitor->monitor_type = $request->input('monitor_type');
    $monitor->monitor_source = $request->input('monitor_source');
    $monitor->monitor_port = $request->input('monitor_port');
    $monitor->monitor_schedule = $request->input('monitor_schedule', 15);
    $monitor->monitor_alert = $request->input('monitor_alert', 3);
    
    $monitor->updateNextRun();
    $monitor->updateState(); // updateState since this is a new monitor it will attempt to enable after running validation checks

    $monitor->save();

    return $monitor;
});

Route::middleware('auth:sanctum')->post('monitors/edit', function(Request $request) {

    $user = $request->user();
    $id = $request->input('id');

    $monitor = Monitor::find($id);

    $monitor->monitor_type = $request->input('monitor_type');
    $monitor->monitor_source = $request->input('monitor_source');
    $monitor->monitor_port = $request->input('monitor_port');
    $monitor->monitor_schedule = $request->input('monitor_schedule', 15);
    $monitor->monitor_alert = $request->input('monitor_alert', 3);
    
    //$monitor->updateNextRun();
    $monitor->updateState($request->input('monitor_state', 3)); //updateState we pass the requested state running validation checks e.g. can this monitor be enabled or not

    $monitor->update();

    return $monitor;
});

Route::middleware('auth:sanctum')->post('monitors/delete', function(Request $request) {

    $user = $request->user();
    $monitor = new Monitor;
    $monitor->where('monitor_id', $request->input('monitor_id'))->where('user_id', $user->id)->delete();

    return $monitor;
});

// This is an api route to trigger monitor runs per slave on geolocated servers triggered by a master server
// This is TODO but several servers would be setup with a clone of this project and mapped to a db list of these servers
// The master server via cron would loop through this list making a call to the api on each thus executing monitors
Route::middleware('auth:sanctum')->post('monitors/runtime', function(Request $request) {

    $user = $request->user();
    $monitor_slave_id = $request->monitor_slave_id();

    $monitors['dns_monitor_path'] = base_path() . '/monitors/dns-monitor.py';
    $monitors['http_monitor_path'] = base_path() . '/monitors/http-monitor.py';
    $monitors['ping_monitor_path'] = base_path() . '/monitors/ping-monitor.py';

    foreach($monitors as $monitor)
    {
        $command = escapeshellcmd($monitor);
        $output = shell_exec($command);    
    }

    return true;
});