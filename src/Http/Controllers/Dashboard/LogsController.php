<?php

namespace LarraPress\BlogPoster\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class LogsController extends Controller
{
    public function dashboard()
    {
        return view('blog-poster::dashboard')->with([
            'title' => 'Logs'
        ]);
    }
}
