<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaypalWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->all();
        $eventType = $payload['event_type'] ?? null;

        if ($eventType !== 'PAYMENT.CAPTURE.COMPLETED') {
            return response('ok', 200);
        }

        // Verificar autenticidad del webhook con PayPal
        if (! $this->verifyWebhook($request)) {
            Log::warning('paypal.webhook.invalid_signature', ['headers' => $request->headers->all()]);
            return response('Invalid webhook', 400);
        }

        $resource = $payload['resource'] ?? [];
        $transactionId = $resource['id'] ?? null;
        $status = $resource['status'] ?? null;

        if ($status !== 'COMPLETED' || ! $transactionId) {
            return response('ok', 200);
        }

        // El user_id viene en custom_id de la purchase_unit
        $userId = (int) ($resource['purchase_units'][0]['custom_id']
            ?? $resource['custom_id']
            ?? 0);

        if (! $userId) {
            Log::error('paypal.webhook.missing_user_id', ['transaction_id' => $transactionId]);
            return response('Missing user_id', 422);
        }

        $confirmed = isset($resource['amount']['value']) ? (float) $resource['amount']['value'] : null;
        $currency  = $resource['amount']['currency_code'] ?? null;

        try {
            OrderService::storeOrder($transactionId, 'paypal', $userId, $confirmed, $currency);
        } catch (\Throwable $e) {
            Log::error('paypal.webhook.order_failed', [
                'transaction_id' => $transactionId,
                'user_id'        => $userId,
                'error'          => $e->getMessage(),
            ]);
            return response('Order processing failed', 500);
        }

        return response('ok', 200);
    }

    private function verifyWebhook(Request $request): bool
    {
        $mode = config('paypal.mode', 'sandbox');
        $baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $clientId     = config("paypal.{$mode}.client_id");
        $clientSecret = config("paypal.{$mode}.client_secret");

        $tokenResponse = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        $accessToken = $tokenResponse->json('access_token');

        if (! $accessToken) {
            return false;
        }

        $verifyResponse = Http::withToken($accessToken)
            ->post("{$baseUrl}/v1/notifications/verify-webhook-signature", [
                'auth_algo'         => $request->header('PAYPAL-AUTH-ALGO'),
                'cert_url'          => $request->header('PAYPAL-CERT-URL'),
                'transmission_id'   => $request->header('PAYPAL-TRANSMISSION-ID'),
                'transmission_sig'  => $request->header('PAYPAL-TRANSMISSION-SIG'),
                'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
                'webhook_id'        => config('paypal.webhook_id', ''),
                'webhook_event'     => $request->all(),
            ]);

        return $verifyResponse->json('verification_status') === 'SUCCESS';
    }
}
