<?php

namespace App\Rules;

use App\Services\DatabaseServices\ListDatabaseUsersService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserDoesNotExist implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $users = (new ListDatabaseUsersService())->execute();
            if (in_array($value, $users, true)) {
                $fail("The user already exists.");
            }
        } catch (Exception $e) {
            $fail("An error occurred while verifying the user existence.");
        }
    }
}
