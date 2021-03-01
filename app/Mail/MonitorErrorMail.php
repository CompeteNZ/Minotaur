<?php

namespace Minotaur\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonitorErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $monitor;
    protected $results;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($monitor, $result)
    {
        //
        $this->monitor = $monitor;
        $this->result = $result;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.alerts.error')->with([
            'monitor_id' => $this->monitor->monitor_id,
            'monitor_type' => strtoupper($this->monitor->monitor_type),
            'result_time' => $this->result->created_at,
            'result_error' => $this->result->monitor_result? 'HOST UNKNOWN' : 'DOWN',
        ]);;
    }
}
