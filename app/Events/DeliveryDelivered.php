<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Delivery;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Event;

class DeliveryDelivered extends Event
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(protected Delivery $delivery)
    {
    }
}
