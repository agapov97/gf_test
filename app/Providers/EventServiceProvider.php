<?php

namespace App\Providers;

use App\Models\Delivery;
use App\Observers\DeliveryObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventServiceProviderAlias;

class EventServiceProvider extends BaseEventServiceProviderAlias
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Delivery::observe(DeliveryObserver::class);
    }
}
