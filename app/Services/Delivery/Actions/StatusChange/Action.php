<?php

namespace App\Services\Delivery\Actions\StatusChange;

use App\Models\Delivery;

class Action
{
    public function handle(Delivery $delivery, InputData $inputData): void
    {
        $delivery->status->transitionTo($inputData->status, $inputData->extraData);
    }
}
