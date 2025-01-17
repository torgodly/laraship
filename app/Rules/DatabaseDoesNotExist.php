<?php

namespace App\Rules;

use App\Services\DatabaseServices\ListDatabasesService;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

class DatabaseDoesNotExist implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $databases = (new ListDatabasesService())->execute();
            if (in_array($value, $databases, true)) {
                $fail("The database already exists.");
            }
        } catch (Exception $e) {
            $fail("An error occurred while verifying the database existence.");
        }
    }
}
