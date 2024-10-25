<?php

namespace App\Rules;

use App\Models\Plate;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MenuPlateRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    protected $restaurantId;

    public function __construct($restaurantId)
    {
        $this->restaurantId = $restaurantId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = Plate::where('id', $value)
            ->where('restaurant_id', $this->restaurantId)
            ->exists();
        if (!$exists) {
            $fail('Este platillo no te pertence');
        }
    }
}
