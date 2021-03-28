<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Minotaur\Monitor;

class MonitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $monitors = array(
            'propertyconsultants.nz',
            'sharedspace.co.nz',
            'energise.co.nz',
            'realestate.co.nz',
            'pdccreative.co.nz',
            'kiwiblog.co.nz',
            'kaipara.govt.nz',
            'autofocus.net.nz',
            'easyrent.nz',
            'thebfd.co.nz',
            'gomonster.nz');

        foreach($monitors as $monitor_source)
        {
            $monitor = new Monitor;
            $monitor->user_id = 1;
            $monitor->monitor_id = strtoupper(Str::random(12));
            $monitor->monitor_type = 'http';
            $monitor->monitor_source = $monitor_source;
            $monitor->monitor_port = 443;
            $monitor->monitor_schedule = 1;
            $monitor->monitor_alert = 1;
            
            $monitor->updateNextRun();
            $monitor->updateState(); // updateState since this is a new monitor it will attempt to enable after running validation checks
        
            $monitor->save();
        }

        foreach($monitors as $monitor_source)
        {
            $monitor = new Monitor;
            $monitor->user_id = 1;
            $monitor->monitor_id = strtoupper(Str::random(12));
            $monitor->monitor_type = 'ping';
            $monitor->monitor_source = $monitor_source;
            $monitor->monitor_port = 443;
            $monitor->monitor_schedule = 1;
            $monitor->monitor_alert = 1;
            
            $monitor->updateNextRun();
            $monitor->updateState(); // updateState since this is a new monitor it will attempt to enable after running validation checks
        
            $monitor->save();
        }

        foreach($monitors as $monitor_source)
        {
            $monitor = new Monitor;
            $monitor->user_id = 1;
            $monitor->monitor_id = strtoupper(Str::random(12));
            $monitor->monitor_type = 'dns';
            $monitor->monitor_source = $monitor_source;

            $monitor->monitor_schedule = 1;
            $monitor->monitor_alert = 1;
            
            $monitor->updateNextRun();
            $monitor->updateState(); // updateState since this is a new monitor it will attempt to enable after running validation checks
        
            $monitor->save();
        }
    }
}
