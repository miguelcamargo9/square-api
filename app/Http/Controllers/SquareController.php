<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Square\SquareClient;
use Square\Environment;
use Square\Models\Money;
use Square\Models\Currency;
use Square\Models\CreatePaymentRequest;

class SquareController extends Controller
{
    //
    public function payment(Request $request)
    {

        $client = new SquareClient([
            'accessToken' => 'EAAAEIlbibDIqswGrhIUZ6W5NV7zxDpduYKOhm4E9gMty-nNeTn1nwghvAHSCk7s',
            'environment' => Environment::SANDBOX,
        ]);

        $paymentsApi = $client->getPaymentsApi();

        $body_sourceId = $request->input('nonce');
        $body_idempotencyKey = uniqid();
        $body_amountMoney = new Money;
        $body_amountMoney->setAmount($request->input('price'));
        $body_amountMoney->setCurrency(Currency::USD);
        $body = new CreatePaymentRequest(
            $body_sourceId,
            $body_idempotencyKey,
            $body_amountMoney
        );

        $apiResponse = $paymentsApi->createPayment($body);

        if ($apiResponse->isSuccess()) {
            $createPaymentResponse = $apiResponse->getResult();
            return response()->json($createPaymentResponse, 200);
        } else {
            $errors = $apiResponse->getErrors();
            return response()->json($errors, 500);
        }
    }
}
