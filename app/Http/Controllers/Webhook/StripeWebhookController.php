<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('stripe.webhook_secret');

        try {
            Stripe::setApiKey(config('stripe.secret'));
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            Log::warning('stripe.webhook.invalid_signature', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        } catch (\UnexpectedValueException $e) {
            Log::warning('stripe.webhook.invalid_payload', ['error' => $e->getMessage()]);
            return response('Invalid payload', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            if ($session->payment_status !== 'paid') {
                return response('ok', 200);
            }

            $userId = (int) ($session->metadata->user_id ?? 0);
            $transactionId = $session->payment_intent;

            if (! $userId || ! $transactionId) {
                Log::error('stripe.webhook.missing_data', [
                    'session_id' => $session->id,
                    'user_id'    => $userId,
                ]);
                return response('Missing data', 422);
            }

            $confirmed = isset($session->amount_total) ? (float) $session->amount_total / 100 : null;
            $currency  = isset($session->currency) ? strtoupper($session->currency) : null;

            try {
                OrderService::storeOrder($transactionId, 'stripe', $userId, $confirmed, $currency);
            } catch (\Throwable $e) {
                Log::error('stripe.webhook.order_failed', [
                    'transaction_id' => $transactionId,
                    'user_id'        => $userId,
                    'error'          => $e->getMessage(),
                ]);
                return response('Order processing failed', 500);
            }
        }

        return response('ok', 200);
    }
}
