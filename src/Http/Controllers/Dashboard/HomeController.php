<?php

namespace LarraPress\BlogPoster\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use LarraPress\BlogPoster\Models\ScrapingJob;

class HomeController extends Controller
{
    public function dashboard()
    {
        $jobs = ScrapingJob::all();

        return view('blog-poster::dashboard')
            ->with([
                'title' => 'Dashboard',
                'jobs' => $jobs
            ]);
    }
}
