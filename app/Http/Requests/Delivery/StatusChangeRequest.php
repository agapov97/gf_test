<?php

namespace App\Http\Requests\Delivery;

use App\Models\Delivery;
use App\Rules\StateTransitionExists;
use App\Services\Delivery\Actions\StatusChange\InputData as StatusChangeInputData;
use App\Services\Delivery\States\DeliveryState;
use App\Services\Delivery\States\Shipped;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Spatie\ModelStates\Validation\ValidStateRule;

class StatusChangeRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Delivery $delivery */
        $delivery = $this->route('delivery');

        $rules = [
            'status' => [
                'bail',
                'required',
                'string',
                new ValidStateRule(DeliveryState::class),
            ],
            'driver_id' => [
                Rule::requiredIf(fn () => $this->input('status') === Shipped::$name),
                'integer',
            ],
        ];

        if ($delivery instanceof Delivery) {
            $rules['status'][] = new StateTransitionExists($delivery->status);
        }

        return $rules;
    }

    public function getInputData(): StatusChangeInputData
    {
        $data = $this->validated();

        return new StatusChangeInputData(
            $data['status'],
            Arr::except($data, ['status'])
        );
    }
}
