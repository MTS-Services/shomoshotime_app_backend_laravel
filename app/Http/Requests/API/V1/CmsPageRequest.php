<?php

namespace App\Http\Requests\API\V1;

use App\Http\Requests\API\BaseRequest;
use Illuminate\Validation\Rule;

class CmsPageRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $routeName = $this->route()?->getName();

        if ($routeName === 'api.v1.admin.cms-pages.store') {
            return [
                'type' => ['required', 'string', 'max:255', Rule::unique('cms_pages', 'type')],
                'content' => ['required', 'string'],
                'is_active' => ['nullable', 'boolean'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ];
        }

        if ($routeName === 'api.v1.admin.cms-pages.update') {
            $ignoreId = $this->input('id');

            return [
                'id' => ['required', 'integer', 'exists:cms_pages,id'],
                'type' => ['sometimes', 'string', 'max:255', Rule::unique('cms_pages', 'type')->ignore($ignoreId)],
                'content' => ['required', 'string'],
                'is_active' => ['nullable', 'boolean'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
            ];
        }

        if ($routeName === 'api.v1.admin.cms-pages.show') {
            return [
                'id' => ['required', 'integer', 'exists:cms_pages,id'],
            ];
        }

        if ($routeName === 'api.v1.admin.cms-pages.delete') {
            return [
                'id' => ['required', 'integer', 'exists:cms_pages,id'],
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'The page type is required.',
            'type.in' => 'The selected page type is invalid.',
            'type.unique' => 'The selected page type has already been used.',
            'id.required' => 'The CMS page id is required.',
            'id.exists' => 'The specified CMS page could not be found.',
            'content.required' => 'Content is required.',
        ];
    }
}
