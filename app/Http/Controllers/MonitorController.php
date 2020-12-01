<?php

namespace Minotaur\Http\Controllers;

use Illuminate\Http\Request;
use Minotaur\Monitor;

class MonitorController extends Controller
{
    //
    public function index()
    {
        return Monitor::all();
    }
}
