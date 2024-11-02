<?php

namespace App\Http\Requests;

use App\Rules\MenuPlateRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
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
            "name" => "required|string|max:255",
            "slug" => "required|string",
            "description" => "required|string|max:1500",
            "restaurant_id" => "required|exists:restaurants,id",
            'plate_ids' => 'nullable|array',
            'plate_ids.*' => ['integer', 'exists:plates,id', new MenuPlateRule($this->restaurant_id)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => str($this->get("name").' '.uniqid())->slug()->value(),
        ]);
    }
}
