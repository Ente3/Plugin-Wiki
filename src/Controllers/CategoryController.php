<?php

namespace Azuriom\Plugin\Wiki\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Wiki\Models\Category;
use Azuriom\Plugin\Wiki\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::scopes(['parents', 'enabled'])
            ->get()
            ->filter(fn (Category $cat) => Gate::allows('view', $cat));

        return view('wiki::categories.index', ['categories' => $categories]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $this->authorize('view', $category);

        $page = $category->pages->first();

        if ($page !== null) {
            return to_route('wiki.pages.show', [$category, $page]);
        }

        $subCategory = $category->categories()
            ->enabled()
            ->get()
            ->first(fn (Category $sub) => Gate::allows('view', $sub));

        $page = $subCategory?->pages()->first();

        if ($page === null) {
            return to_route('wiki.index')
                ->with('error', trans('wiki::messages.search.empty'));
        }

        return to_route('wiki.pages.show', [$subCategory, $page]);
    }

    /**
     * Find the resources for the specified query.
     */
    public function search(Request $request)
    {
        $search = $request->input('q');

        if ($search === null) {
            return to_route('wiki.index');
        }

        $categoryIds = Category::enabled()
            ->get()
            ->filter(fn (Category $category) => Gate::allows('view', $category))
            ->modelKeys();

        $pages = Page::search($search)
            ->whereIn('category_id', $categoryIds)
            ->with('category')
            ->paginate();

        return view('wiki::pages.search', [
            'pages' => $pages,
            'search' => $search,
        ]);
    }
}
