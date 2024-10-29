<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Spatie\ModelStates\State;

class StateTransitionExists implements ValidationRule
{
    public function __construct(private State $currentState)
    {
    }

    /**
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->currentState->canTransitionTo($value, [])) {
            $fail(
                "Изменение {$attribute} с '{$this->currentState->getValue()}' на '{$value}' не возможно."
            );
        }
    }
}
