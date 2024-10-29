<?php

namespace App\Tests;

use App\Events\DeliveryDelivered;
use App\Models\Delivery;
use App\Services\Delivery\States\Delivered;
use App\Services\Delivery\States\DeliveryState;
use App\Services\Delivery\States\Planned;
use App\Services\Delivery\States\Shipped;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class DeliveryTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Event::fake([DeliveryDelivered::class]);
    }

    private static function getTransitionName(string $fromStatus, string $toStatus): string
    {
        return 'from ' . $fromStatus . ' to ' . $toStatus;
    }

    public static function correctFlowDataProvider(): Collection
    {
        return collect([
            [
                'fromStatus' => Planned::$name,
                'toStatus' => Shipped::$name,
                'extraParams' => [
                    'driver_id' => 1,
                ],
                'shouldRaiseEvent' => false,
            ],
            [
                'fromStatus' => Shipped::$name,
                'toStatus' => Delivered::$name,
                'extraParams' => [],
                'shouldRaiseEvent' => true,
            ],
        ])->keyBy(fn ($row) => self::getTransitionName($row['fromStatus'], $row['toStatus']));
    }

    /**
     * @dataProvider correctFlowDataProvider
     */
    public function testCorrectFlow(
        string $fromStatus,
        string $toStatus,
        array $extraParams = [],
        bool $shouldRaiseEvent = false
    ) {
        $delivery = Delivery::factory()->create(['status' => $fromStatus]);

        $response = $this->post('deliveries/' . $delivery->id . '/status-change', ['status' => $toStatus] + $extraParams);

        $response->assertOk();
        $this->assertDatabaseHas('deliveries', ['id' => $delivery->id, 'status' => $toStatus]);

        if ($shouldRaiseEvent) {
            Event::assertDispatched(DeliveryDelivered::class);
        } else {
            Event::assertNotDispatched(DeliveryDelivered::class);
        }
    }

    public function testCantTransitionToShippedWithoutDriverId()
    {
        $delivery = Delivery::factory()->create(['status' => Planned::$name]);

        $response = $this->post('deliveries/' . $delivery->id . '/status-change', ['status' => Shipped::$name]);

        $response->assertJsonMissingValidationErrors('status');
        $response->assertJsonValidationErrors('driver_id');
    }

    public function testCantTransitionUnknownDelivery()
    {
        $response = $this->post('deliveries/1/status-change', ['status' => Shipped::$name]);

        $response->assertNotFound();
    }

    public function testCantTransitionToUnknownStatus()
    {
        $delivery = Delivery::factory()->create(['status' => Planned::$name]);

        $response = $this->post('deliveries/' . $delivery->id . '/status-change', ['status' => 'i_am_unknown_status']);

        $response->assertJsonValidationErrors('status');
    }

    public static function outOfOrderDataProvider(): \Generator
    {
        $states = DeliveryState::all();
        $allowedTransitions = self::correctFlowDataProvider();

        foreach ($states as $fromStatus => $fromStatusClassName) {
            foreach ($states as $toStatus => $toStatusClassName) {
                $transitionName = self::getTransitionName($fromStatus, $toStatus);

                if (isset($allowedTransitions[$transitionName])) {
                    continue;
                }

                yield $transitionName => [
                    'fromStatus' => $fromStatus,
                    'toStatus' => $toStatus,
                ];
            }
        }
    }

    /**
     * @dataProvider outOfOrderDataProvider
     */
    public function testCantTransitionOutOfOrder(
        string $fromStatus,
        string $toStatus,
    ) {
        $delivery = Delivery::factory()->create(['status' => $fromStatus]);

        $response = $this->post('deliveries/' . $delivery->id . '/status-change', ['status' => $toStatus]);

        $response->assertJsonValidationErrors('status');
    }
}
