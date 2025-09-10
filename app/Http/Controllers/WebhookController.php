<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class WebhookController extends CashierWebhookController
{
    public function handleCheckoutSessionCompleted(array $payload)
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $plan = $payload['data']['object']['metadata']['plan'] ?? 'free';
            $user->profile()->update(['plan' => $plan]);
        }

        return response('Webhook Handled', 200);
    }
}
