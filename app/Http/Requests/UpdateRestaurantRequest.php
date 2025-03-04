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
            'name' => ["unique:restaurants,name", "string", "max:50"],
            'description' => ["string", "max:50"],
            'slug' => ["string", "required"],
        ];
    }

    public function prepareForValidation() {
        if($this->name){
            $this->merge([
             "slug" => str($this->name)->slug()->value()
            ]);
        }
    }
}
