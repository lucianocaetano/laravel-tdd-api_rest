<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantRequest extends FormRequest
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
            'name' => ['nullable', 'required_without_all:description', "string", "max:50"],
            'description' => ['nullable', 'required_without_all:name', "string", "max:50"],
            'slug' => ["string", "required"],
        ];
    }

    public function prepareForValidation() {
        if($this->name){
            $this->merge([
             "slug" => str($this->name . uniqid())->slug()->value()
            ]);
        }
    }
}
