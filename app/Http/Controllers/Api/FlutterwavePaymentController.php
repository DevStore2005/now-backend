<?php

namespace App\Http\Controllers\Api;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class FlutterwavePaymentController extends Controller
{

    public function webhook(Request $request)
    {
        $secretHash = config('common.flutterwave.secret_hash');
        $signature = $request->header('verif-hash');
        if (!$signature || ($signature !== $secretHash)) {
            abort(401);
        }

        $request->validate([
            'data' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $payload = $request->all();
            $existingTransaction = Transaction::query()->where('fw_transaction_id', $payload['data']['id'])->first();
            if (isset($existingTransaction)) {
                return response(200);
            } else {
                Transaction::updateOrCreate([
                    'fw_transaction_id' => $payload['data']['id'],
                ], [
                    'amount' => $payload['data']['amount'] ?? 0,
                    'amount_captured' => $payload['data']['charged_amount'] ?? 0,
                    'status' => $payload['data']['status'] ?? 'failed',
                    'payment_method' => $payload['data']['payment_type'] ?? '',
                    'fw_transaction_id' => $payload['data']['id'] ?? null,
                    'card_info' => $payload['data']['card'] ?? null,
                    'customer_info' => $payload['data']['customer'] ?? null,
                ]);
                DB::commit();
                return response(200);
            }
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error(['FlutterwavePaymentController -> webhook ', $exception->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
            'amount' => 'required|numeric',
            'currency' => 'required',
        ]);

        $expectedAmount = (int)$request->input('amount');
        $expectedCurrency = $request->input('currency');
        $transactionId = $request->input('transaction_id');
        $secret_key = config('common.flutterwave.secret_key');

        DB::beginTransaction();
        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Authorization" => "Bearer $secret_key"
            ])->get("https://api.flutterwave.com/v3/transactions/$transactionId/verify");
            $data = $response->json();
            if (
                $response->successful()
                && $data['data']['status'] === "successful"
                && $data['data']['amount'] === $expectedAmount
                && $data['data']['currency'] === $expectedCurrency) {
                Transaction::updateOrCreate([
                    'fw_transaction_id' => $data['data']['id'],
                ], [
                    'user_id' => auth()->user()->id,
                    'amount' => $data['data']['amount'] ?? 0,
                    'amount_captured' => $data['data']['charged_amount'] ?? 0,
                    'status' => $data['data']['status'] ?? 'failed',
                    'payment_method' => $data['data']['payment_type'] ?? '',
                    'fw_transaction_id' => $data['data']['id'] ?? null,
                    'card_info' => $data['data']['card'] ?? null,
                    'customer_info' => $data['data']['customer'] ?? null,
                ]);
                DB::commit();
                return response()->json([
                    'error' => false,
                    'message' => $data['message'],
                ], $response->status());
            } else {
                DB::rollback();
                return response()->json([
                    'error' => true,
                    'message' => $response->status() === 200 ? "The information does not match!" : $data['message'],
                ], $response->status() === 200 ? HttpStatusCode::NOT_FOUND : $response->status());
            }
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error(['FlutterwavePaymentController -> verify ', $exception->getMessage()]);
            return response()->json([
                'error' => true,
                'message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]
            ], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
