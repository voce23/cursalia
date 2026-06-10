<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
        $secret = config('razorpay.webhook_secret');

        // Verificar firma HMAC-SHA256
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expectedSignature, (string) $signature)) {
            Log::warning('razorpay.webhook.invalid_signature');

            return response('Invalid signature', 400);
        }

        $data = $request->all();
        $eventType = $data['event'] ?? null;

        if ($eventType !== 'payment.captured') {
            return response('ok', 200);
        }

        $payment = $data['payload']['payment']['entity'] ?? [];
        $transactionId = $payment['id'] ?? null;
        $status = $payment['status'] ?? null;

        if ($status !== 'captured' || ! $transactionId) {
            return response('ok', 200);
        }

        // user_id viene en notes (agregado al crear la orden de Razorpay)
        $userId = (int) ($payment['notes']['user_id'] ?? 0);

        if (! $userId) {
            Log::error('razorpay.webhook.missing_user_id', ['transaction_id' => $transactionId]);

            return response('Missing user_id', 422);
        }

        try {
            OrderService::storeOrder($transactionId, 'razorpay', $userId);
        } catch (\Throwable $e) {
            Log::error('razorpay.webhook.order_failed', [
                'transaction_id' => $transactionId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return response('Order processing failed', 500);
        }

        return response('ok', 200);
    }
}
