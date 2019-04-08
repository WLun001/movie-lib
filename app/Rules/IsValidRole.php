<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsValidRole implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, ['admin', 'staff', 'member']);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'only admin, staff, member role are allowed.';
    }
}
