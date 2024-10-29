<?php

namespace App\Services\Delivery\States;

use App\Services\Delivery\StateTransitions\ToShippedTransition;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class DeliveryState extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Planned::class)
            ->allowTransition(Planned::class, Shipped::class, ToShippedTransition::class)
            ->allowTransition(Shipped::class, Delivered::class)
        ;
    }
}
