<?php

namespace LarraPress\BlogPoster\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use LarraPress\BlogPoster\Crawler;
use LarraPress\BlogPoster\Crawler\ArticleAttribute;
use LarraPress\BlogPoster\Models\ScrapingJob;

class JobsController extends Controller
{
    public function create()
    {
        $categories = Category::select('id', 'name')->get();

        return view('blog-poster::add_edit_job')
            ->with([
                'title' => 'New Job',
                'categories' => $categories
            ]);
    }

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
     * @throws GuzzleException
     */
    public function test(Request $request): Collection
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

    public function copy($id, Request $request)
    {
        $categories = Category::select('id', 'name')->get();
        $job = ScrapingJob::findOrFail($id);

        return view('blog-poster::add_edit_job')
            ->with([
                'title' => 'Edit '. $job->name .' Job',
                'job' => $job,
                'copying' => true,
                'categories' => $categories
            ]);
    }

    public function edit($id, Request $request)
    {
        $categories = Category::select('id', 'name')->get();
        $job = ScrapingJob::findOrFail($id);

        return view('blog-poster::add_edit_job')
            ->with([
                'title' => 'Edit '. $job->name .' Job',
                'job' => $job,
                'categories' => $categories
            ]);
    }

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
