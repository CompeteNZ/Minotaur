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
        switch($this->result->monitor_result)
        {
            case "-1":
                $result_error = 'HOST NOT FOUND / HOST UNKNOWN';
                break;
            case "0":
                $result_error = 'HOST IS DOWN / COULD NOT CONNECT';
            default:
                $result_error = $this->result->monitor_result;
            break;
        }

        return $this->view('emails.alerts.error')->with([
            'monitor_id' => $this->monitor->monitor_id,
            'monitor_type' => strtoupper($this->monitor->monitor_type),
            'monitor_source' => $this->monitor->monitor_source,
            'result_time' => $this->result->created_at,
            'result_error' => $result_error,
        ]);;
    }
}
