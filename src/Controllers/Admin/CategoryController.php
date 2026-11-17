<?php

namespace Azuriom\Plugin\Wiki\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Role;
use Azuriom\Plugin\Wiki\Models\Category;
use Azuriom\Plugin\Wiki\Requests\CategoryRequest;
use Illuminate\Support\Arr;

class CategoryController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('wiki::admin.categories.create', [
            'categories' => Category::parents()->get(),
            'roles' => Role::orderByDesc('power')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        Category::create($this->categoryData($request));

        return to_route('wiki.admin.pages.index')
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('wiki::admin.categories.edit', [
            'category' => $category,
            'categories' => Category::parents()->get()->except($category->id),
            'roles' => Role::orderByDesc('power')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($this->categoryData($request, $category));

        return to_route('wiki.admin.pages.index')
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \LogicException
     */
    public function destroy(Category $category)
    {
        $category->deleteImageFile();

        $category->delete();

        return to_route('wiki.admin.pages.index')
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Get the data to save for a category, including its uploaded image or image link.
     *
     * @return array<string, mixed>
     */
    private function categoryData(CategoryRequest $request, ?Category $category = null): array
    {
        $data = Arr::except($request->validated(), ['image_file', 'image_link']);

        if ($request->hasFile('image_file')) {
            $data['image'] = $request->file('image_file')->store('wiki/categories', 'public');
        } elseif ($request->filled('image_link')) {
            $data['image'] = $request->input('image_link');
        } elseif ($category?->hasExternalImage()) {
            // The link field is pre-filled in the form, so an empty one removes the link.
            $data['image'] = null;
        }

        // Remove the previous uploaded file when the image is replaced.
        if ($category !== null && array_key_exists('image', $data) && $data['image'] !== $category->image) {
            $category->deleteImageFile();
        }

        return $data;
    }
}
