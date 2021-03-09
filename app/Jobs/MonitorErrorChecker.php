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

        // get results for each monitor and check for errors
        foreach($monitors as $monitor)
        {
            // get last two results
            $results = DB::table('monitor_results')->select()
                ->where('monitor_id', '=', $monitor->monitor_id)
                ->where('monitor_type', '=', 'dns')
                ->orderByDesc('created_at')
                ->limit(1)
                ->get();

            if($results[0]->monitor_result <= 0)
            {
                Log::error('Error found with monitor id: ' . $monitor->monitor_id);
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

        // get results for each monitor and check for errors
        foreach($monitors as $monitor)
        {
            // get last two results
            $results = DB::table('monitor_results')->select()
                ->where('monitor_id', '=', $monitor->monitor_id)
                ->where('monitor_type', '=', 'ping')
                ->orderByDesc('created_at')
                ->limit(1)
                ->get();
            
            if($results[0]->monitor_result <= 0)
            {
                Log::error('Error found with monitor id: ' . $monitor->monitor_id);
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

        // get results for each monitor and check for errors
        foreach($monitors as $monitor)
        {
            // get last two results
            $results = DB::table('monitor_results')->select()
                ->where('monitor_id', '=', $monitor->monitor_id)
                ->where('monitor_type', '=', 'http')
                ->orderByDesc('created_at')
                ->limit(1)
                ->get();

            if($results[0]->monitor_result != 200)
            {
                Log::error('Error found with monitor id: ' . $monitor->monitor_id);
                Mail::to("pete@davisonline.co.nz")->send(new MonitorErrorMail($monitor, $results[0]));
            }
        }
    }
}
