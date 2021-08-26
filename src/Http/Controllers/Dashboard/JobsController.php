<?php

namespace LarraPress\BlogPoster\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use LarraPress\BlogPoster\Crawler;
use LarraPress\BlogPoster\Crawler\ArticleAttribute;
use LarraPress\BlogPoster\Models\ScrapingJob;

class JobsController extends Controller
{
    /**
     * @return View|Factory
     */
    public function create()
    {
        $returnWith = ['title' => 'New Job'];

        if(! is_null(config('blog-poster.category'))) {
            $categories = config('blog-poster.category')::select('id', 'name')->get();
            $returnWith['categories'] = $categories;
        }

        return view('blog-poster::add_edit_job')
            ->with($returnWith);
    }

    /**
     * @param Request $request
     * @return Redirector|RedirectResponse
     */
    public function store(Request $request)
    {
        $request->merge([
            'is_draft' => $request->input('is_draft') === 'on' ? 1 : 0,
            'config' => json_decode($request->input('config'))
        ]);

        $job = new ScrapingJob();
        $job->fill($request->input());

        $job->save();

        return redirect(route('blog-poster.dashboard'));
    }

    /**
     * Set the testing job to the crawler.
     * Only one result will be returned and the files will not be downloaded to the storage.
     *
     * @param Request $request
     * @return array
     * @throws GuzzleException
     */
    public function test(Request $request): array
    {
        $articleAttributes = [];
        $fileAttributes = new Collection();

        foreach (json_decode($request->input('payload'), true) as $configItem) {
            $articleAttribute = new ArticleAttribute($configItem['name']);
            $articleAttribute->setSelector($configItem['selector'])
                ->isFile($configItem['is_file'])
                ->isHtml($configItem['is_html'])
                ->asThumbnail($configItem['as_thumb']);

            if (trim($configItem['type']) !== "") {
                $articleAttribute->setType(trim($configItem['type']));
            }

            if (trim($configItem['custom_tag']) !== "") {
                $articleAttribute->setTagAttribute(trim($configItem['custom_tag']));
            }

            if (! empty($configItem['ignoring_attributes'])) {
                foreach ($configItem['ignoring_attributes'] as $ignoringAttribute) {
                    $articleAttribute->setIgnoringNode($ignoringAttribute);
                }
            }

            if ($configItem['is_file'] === true) {
                $fileAttributes->push($articleAttribute->getName());
            }

            if (! empty($configItem['replacing_attributes'])) {
                foreach ($configItem['replacing_attributes'] as $replacingAttribute) {
                    $articleAttribute->setReplacingAttribute(
                        $replacingAttribute["selector"],
                        $replacingAttribute["replacing_attribute"],
                        $replacingAttribute["attribute_to_get_value_from"],
                    );
                }
            }

            $articleAttributes[] = $articleAttribute;
        }

        $crawler = new Crawler();

        return $crawler->enableTestingMode()
            ->setArticleAttributes($articleAttributes)
            ->setListUri($request->input('source'))
            ->setArticleIdentifierInListSelector($request->input('list_item_identifier'))
            ->run();
    }

    /**
     * Copy scraping job from another one.
     * Get all fields and configs and pass to job creation view.
     *
     * @param mixed $id
     * @return View|Factory
    */
    public function copy($id)
    {
        $job = ScrapingJob::findOrFail($id);

        $returnWith = [
            'title' => 'Edit '. $job->name .' Job',
            'job' => $job,
            'copying' => true,
        ];

        if(! is_null(config('blog-poster.category'))) {
            $categories = config('blog-poster.category')::select('id', 'name')->get();
            $returnWith['categories'] = $categories;
        }

        return view('blog-poster::add_edit_job')
            ->with($returnWith);
    }

    /**
     * @param mixed $id
     * @return View|Factory
    */
    public function edit($id)
    {
        $job = ScrapingJob::findOrFail($id);

        $returnWith = [
            'title' => 'Edit '. $job->name .' Job',
            'job' => $job,
        ];

        if(! is_null(config('blog-poster.category'))) {
            $categories = config('blog-poster.category')::select('id', 'name')->get();
            $returnWith['categories'] = $categories;
        }

        return view('blog-poster::add_edit_job')
            ->with($returnWith);
    }

    /**
     * @param Request $request
     * @return Redirector|RedirectResponse
     */
    public function update(Request $request)
    {
        $request->merge([
            'is_draft' => $request->input('is_draft') === 'on' ? 1 : 0,
            'config' => json_decode($request->input('config'))
        ]);

        $job = ScrapingJob::findOrFail($request->route('id'));
        $job->fill($request->input());
        $job->save();

        return redirect(route('blog-poster.dashboard'));
    }

    /**
     * @param Request $request
    */
    public function delete(Request $request): void
    {
        ScrapingJob::whereId($request->route('id'))->delete();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function parseSourceIcon(Request $request): JsonResponse
    {
        return response()->json([
            'url' => Crawler\SourceIconParser::parseIconUrl($request->input('source_url'))
        ]);
    }
}
