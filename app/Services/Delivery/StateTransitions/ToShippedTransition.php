<?php

namespace App\Services\Delivery\StateTransitions;

use App\Models\Delivery;
use App\Services\Delivery\States\Shipped;
use Spatie\ModelStates\DefaultTransition;

class ToShippedTransition extends DefaultTransition
{
    public function __construct(Delivery $model, private array $extra)
    {
        parent::__construct($model, 'status', new Shipped($model));
    }

    public function handle()
    {
        // Делаем что нужно с $this->extra['driver_id'];

        return parent::handle();
    }
}
