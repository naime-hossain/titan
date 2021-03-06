<?php

namespace Bpocallaghan\Titan\Http\Controllers\Website;

use Bpocallaghan\Titan\Models\Article;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BlogController extends WebsiteController
{
    public function index()
    {
        $perPage = 6;
        $page = input('page', 1);
        $baseUrl = config('app.url') . '/blog';
        $items = Article::whereHas('photos')->with('photos')->active()->orderBy('active_from', 'DESC')->get();

        $total = $items->count();

        // paginator
        $paginator = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(),
            $perPage, $page, ['path' => $baseUrl, 'originalEntries' => $total]);

        // if pagination ajax
        if (request()->ajax()) {
            return response()->json(view('website.blog.pagination')
                ->with('paginator', $paginator)
                ->render());
        }

        return $this->view('titan::blog.blog')->with('paginator', $paginator);
    }

    public function show($newsSlug)
    {
        $item = Article::with('photos')->where('slug', $newsSlug)->first();
        if (!$item) {
            return redirect('/blog');
        }

        $this->addBreadcrumbLink($item->title, false);

        return $this->view('titan::blog.article_show')->with('article', $item);
    }
}
