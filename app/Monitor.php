<?php

namespace Minotaur;

use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    protected $fillable = ['monitor_type','monitor_source','monitor_schedule','monitor_alert','monitor_last','monitor_next','monitor_state'];

    // internal logic to determine if this monitor can be activated or not e.g. verified source, billing status etc and sets a valid state
    public function updateState($requestedState = null)
    {

        // valid states
        // -1 = disabled by system
        // 0 = paused by user
        // 1 = activated

        if($requestedState == null)
        {
            $this->monitor_state = 1; // this is a new monitor so validation checks may be less strict
        }
        else
        {
            $this->monitor_state = $requestedState; // only set this after running strict validation checks
        }
    }
    
    // update expected next run time of this monitor
    public function updateNextRun()
    {
        
        if(!$this->monitor_last)
        {
            //date_default_timezone_set('Pacific/Auckland'); // this should be removed for production or set to UTF
            $this->monitor_last = date("Y-m-d H:i:s"); // if none exists
        }
        
        $this->monitor_next = strftime('%Y-%m-%d %H:%M:%S', strtotime("+" . $this->monitor_schedule . " minutes", strtotime($this->monitor_last)));
        $this->monitor_last = strftime('%Y-%m-%d %H:%M:%S', strtotime($this->monitor_last));

    }
}
