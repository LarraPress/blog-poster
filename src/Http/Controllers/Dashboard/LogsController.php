<?php

namespace LarraPress\BlogPoster\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use LarraPress\BlogPoster\Models\ScrapingJobLog;

class LogsController extends Controller
{
    /**
     * @return View|Factory
     */
    public function dashboard()
    {
        $logs = ScrapingJobLog::with('job')
            ->orderBy('id', 'DESC')
            ->paginate(50);

        return view('blog-poster::logs')->with([
            'title' => 'Logs',
            'logs' => $logs,
        ]);
    }
}
