<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            "name" => ["max:50", "string"],
            "last_name" => ["max:50", "string"],
            "email" => ["max:100", "email", "unique:users"],
        ];
    }
}
