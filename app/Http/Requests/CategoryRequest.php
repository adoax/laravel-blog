<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validate = [
            'name' => ['required']
        ];

        if ($this->getMethod() === 'PUT') {
            return array_merge_recursive($validate, ['name' => [Rule::unique('categories')->ignore($this->category->id)]]);
        };

        if ($this->getMethod() === "POST" || 'PATCH') {
            return array_merge_recursive($validate, ['name' => ['unique:categories']]);
        };
    }
}
