<?php

namespace Azuriom\Plugin\Wiki\Requests;

use Azuriom\Http\Requests\Traits\ConvertCheckbox;
use Azuriom\Plugin\Wiki\Models\Category;
use Azuriom\Rules\Slug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CategoryRequest extends FormRequest
{
    use ConvertCheckbox;

    /**
     * The attributes represented by checkboxes.
     *
     * @var array<int, string>
     */
    protected array $checkboxes = [
        'is_enabled',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $slugRule = Rule::unique(Category::class)->ignore($this->category, 'slug');

        return [
            'icon' => ['nullable', 'string', 'max:50'],
            'icon_type' => ['required', Rule::in(['icon', 'image'])],
            'image_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,gif,webp', 'max:2048'],
            'image_link' => ['nullable', 'url', 'max:2000', 'regex:/^https?:\/\//i'],
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['required', 'string', 'max:100', new Slug(), $slugRule],
            'parent_id' => ['nullable', 'exists:wiki_categories,id'],
            'roles' => ['sometimes', 'nullable', 'array'],
            'is_enabled' => ['filled', 'boolean'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('icon_type') !== 'image') {
                return;
            }

            $category = $this->category;
            $hasUploadedImage = $category instanceof Category
                && $category->image !== null
                && ! $category->hasExternalImage();

            if (! $this->hasFile('image_file') && ! $this->filled('image_link') && ! $hasUploadedImage) {
                $validator->errors()->add('image_file', trans('wiki::admin.categories.image_required'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->mergeCheckboxes();

        if (! $this->filled('is_private') || ! $this->has('roles')) {
            $this->merge(['roles' => null]);
        }
    }
}
