<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MobileNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Check if it's exactly 10 digits long
        if (strlen($value) !== 10) {
            $fail("The :attribute must be exactly 10 digits.");
            return; // Stop further checks if length is wrong
        }

        // 2. Check if it contains only digits
        if (!preg_match('/^[0-9]+$/', $value)) {
            $fail("The :attribute must contain only digits.");
            return;
        }

        // If all checks pass, do nothing (validation passes)
    }
}
