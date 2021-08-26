<?php

namespace LarraPress\BlogPoster\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class LogsController extends Controller
{
    /**
     * @return View|Factory
    */
    public function dashboard()
    {
        return view('blog-poster::dashboard')->with([
            'title' => 'Logs'
        ]);
    }
}
