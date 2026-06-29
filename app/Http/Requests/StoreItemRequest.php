<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:item_categories,id'],
            'type_id' => ['required', 'exists:item_types,id'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'item_name' => ['required', 'string', 'max:255'],
            'size' => ['nullable', 'integer'],
            'size_unit' => ['nullable', 'in:ML,GM,KG,LTR'],
            'minimum_qty' => ['required', 'integer', 'min:0'],
        ];
    }
}
