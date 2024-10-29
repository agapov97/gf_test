<?php

namespace App\Models;

use App\Services\Delivery\States\DeliveryState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

/**
 * Модель доставки
 *
 * @property int $id
 * @property-read DeliveryState $status
 */
class Delivery extends Model
{
    use HasFactory;
    use HasStates;

    protected $casts = [
        'status' => DeliveryState::class,
    ];
}
