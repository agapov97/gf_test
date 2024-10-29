<?php

namespace App\Http\Controllers;

use App\Http\Requests\Delivery\StatusChangeRequest;
use App\Models\Delivery;
use App\Services\Delivery\Actions\StatusChange\Action as StatusChangeAction;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Symfony\Component\HttpFoundation\Response;

class DeliveryController extends Controller
{
    public function statusChange(
        StatusChangeRequest $request,
        Delivery $delivery,
        StatusChangeAction $action
    ): \Illuminate\Http\Response {
        $data = $request->getInputData();

        $action->handle($delivery, $data);

        return ResponseFacade::make('', Response::HTTP_OK);
    }
}
