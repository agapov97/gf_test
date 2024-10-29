<?php

namespace App\Services\Delivery\Actions\StatusChange;

use App\Services\Delivery\States\DeliveryState;

readonly class InputData
{
    public function __construct(
        public string|DeliveryState $status,
        public array $extraData
    ) {
    }
}
