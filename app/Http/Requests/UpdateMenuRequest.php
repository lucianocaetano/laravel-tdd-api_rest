<?php

namespace App\Http\Requests;

use App\Rules\MenuPlateRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuRequest extends FormRequest
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
        return [
            "name" => "string|max:255",
            "description" => "string|max:1000",
            "restaurant_id" => "exists:restaurants,id",
            "slug" => "string",
            'plate_ids' => 'nullable|array',
            'plate_ids.*' => ['integer', 'exists:plates,id', new MenuPlateRule($this->restaurant_id)],
        ];
    }
}
