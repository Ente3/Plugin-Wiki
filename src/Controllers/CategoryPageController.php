<?php

namespace Azuriom\Plugin\Wiki\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Wiki\Models\Category;
use Azuriom\Plugin\Wiki\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

class CategoryPageController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Category $category, Page $page)
    {
        // Redirect pages accessed through another category URL
        if (! $page->category()->is($category)) {
            return to_route('wiki.pages.show', [$page->category, $page]);
        }

        $this->authorize('view', $category);

        $page->setRelation('category', $category);

        $categories = $this->visibleCategories();

        $breadcrumb = collect();
        $current = $categories->get($category->id);

        while ($current !== null) {
            $breadcrumb->prepend($current);
            $current = $current->parent_id !== null ? $categories->get($current->parent_id) : null;
        }

        // The category is not in the tree (disabled, or one of its parents is hidden)
        if ($breadcrumb->isEmpty()) {
            $breadcrumb->push($category);
        }

        $siblings = $categories->get($category->id)?->pages ?? $category->pages;

        return view('wiki::pages.show', [
            'page' => $page,
            'tree' => $categories->whereNull('parent_id')->values(),
            'breadcrumb' => $breadcrumb,
            'previous' => $siblings->before(fn (Page $p) => $p->is($page)),
            'next' => $siblings->after(fn (Page $p) => $p->is($page)),
        ]);
    }

    private function visibleCategories(): Collection
    {
        $categories = Category::enabled()
            ->with(['pages' => fn ($query) => $query->select([
                'id', 'category_id', 'title', 'slug',
            ])])
            ->orderBy('position')
            ->get()
            ->filter(fn (Category $cat) => Gate::allows('view', $cat))
            ->keyBy('id');

        // Categories whose parent is hidden are never reached from the
        // roots, so they are hidden too.
        foreach ($categories as $cat) {
            $cat->setRelation('categories', $categories->where('parent_id', $cat->id)->values());
        }

        return $categories;
    }
}
