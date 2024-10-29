<?php

namespace App\Observers;

use App\Events\DeliveryDelivered;
use App\Models\Delivery;
use App\Services\Delivery\States\Delivered;

class DeliveryObserver
{
    public function updated(Delivery $delivery): void
    {
        if (
            $delivery->wasChanged('status') &&
            $delivery->status->equals(Delivered::class)
        ) {
            event(new DeliveryDelivered($delivery));
        }
    }
}
