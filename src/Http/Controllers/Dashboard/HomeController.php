<?php

namespace LarraPress\BlogPoster\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use LarraPress\BlogPoster\Models\ScrapingJob;

class HomeController extends Controller
{
    /**
     * @return View|Factory
     */
    public function dashboard()
    {
        $jobs = ScrapingJob::all();

        return view('blog-poster::dashboard')
            ->with([
                'title' => 'Dashboard',
                'jobs' => $jobs,
            ]);
    }
}
