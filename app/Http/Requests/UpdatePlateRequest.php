<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlateRequest extends FormRequest
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
            'name' => ['sometimes', "string", "max:50"],
            'description' => ['sometimes', "string", "max:50"],
            'image' => ['sometimes', "image", "mimes:jpeg,png,jpg,gif,svg", "max:50"],
            'price' => ['sometimes', "numeric"],
        ];
    }


}
