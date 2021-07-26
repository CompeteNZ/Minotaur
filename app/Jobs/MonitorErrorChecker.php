<?php

namespace Minotaur\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Minotaur\Mail\MonitorErrorMail;

class MonitorErrorChecker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // These functions could be moved to their respective models
        $this->checkDnsErrors();
        $this->checkPingErrors();
        $this->checkHttpErrors();
    }

    public function checkDnsErrors()
    {
        // get monitors
        $monitors = DB::table('monitors')->select()
        ->where('monitor_type', '=', 'dns')
        ->get();

        // get results for each monitor and check for errors in the last 5 minutes
        foreach($monitors as $monitor)
        {
            // get last two results
            $query= DB::table('monitor_results')->select()
                ->where('monitor_id', '=', $monitor->monitor_id)
                ->where('monitor_type', '=', 'dns')
                ->where('created_at', '>=', 'NOW() - INTERVAL 5 MINUTE');
                
            $results_count = $query->count();
            $results = $query->get();

            Log::info("Error check for monitor id: " . $monitor->monitor_id . " results found = " . $results_count);

            if($results_count > 1)
            {
                Mail::to("pete@davisonline.co.nz")->send(new MonitorErrorMail($monitor, $results[0]));             
            }
        }
    }
    
    public function checkPingErrors()
    {
        // get monitors
        $monitors = DB::table('monitors')->select()
        ->where('monitor_type', '=', 'ping')
        ->get();

        // get results for each monitor and check for errors in the last 5 minutes
        foreach($monitors as $monitor)
        {
            // get last two results
            $results = DB::table('monitor_results')->select()
                ->where('monitor_id', '=', $monitor->monitor_id)
                ->where('monitor_type', '=', 'ping')
                ->where('created_at', '>=', 'NOW() - INTERVAL 5 MINUTE')
                ->get();
            
            $results_count = $query->count();
            $results = $query->get();

            Log::info("Error check for monitor id: " . $monitor->monitor_id . " results found = " . $results_count);

            if($results_count > 1)
            {
                Mail::to("pete@davisonline.co.nz")->send(new MonitorErrorMail($monitor, $results[0]));             
            }
        }
    }
    
    public function checkHttpErrors()
    {
        // get monitors
        $monitors = DB::table('monitors')->select()
        ->where('monitor_type', '=', 'http')
        ->get();

        // get results for each monitor and check for errors in the last 5 minutes
        foreach($monitors as $monitor)
        {
            // get last two results
            $results = DB::table('monitor_results')->select()
                ->where('monitor_id', '=', $monitor->monitor_id)
                ->where('monitor_type', '=', 'http')
                ->where('created_at', '>=', 'NOW() - INTERVAL 5 MINUTE')
                ->get();

            $results_count = $query->count();
            $results = $query->get();

            Log::info("Error check for monitor id: " . $monitor->monitor_id . " results found = " . $results_count);

            if($results_count > 1)
            {
                Mail::to("pete@davisonline.co.nz")->send(new MonitorErrorMail($monitor, $results[0]));             
            }
        }
    }
}
