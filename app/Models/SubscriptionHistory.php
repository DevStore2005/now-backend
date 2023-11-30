<?php

namespace App\Models;

use App\Events\AlertEvent;
use App\Http\Helpers\Fcm;
use App\Models\Commission;
use App\Models\Transaction;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\PaymentFailedNotification;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionHistory extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'providers_subscription_id',
        'service_request_id',
        'transaction_id',
        'deduction_date',
        'discount',
        'status',
        'description'
    ];

    /**
     * SubscriptionHistory belongs to ProvidersSubscription.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function providers_subscription(): BelongsTo
    {
        return $this->belongsTo(ProvidersSubscription::class, 'providers_subscription_id', 'id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    /**
     * SubscriptionHistory belongs to ServiceRequest.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service_request(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function deductPayments($date = null)
    {
        $date ??= now()->subDay()->format('Y-m-d');
        // $failed = fn ($qry) => $qry->orWhere('status', "FAILED");
        $cursor = $this->query()
            ->whereDate('deduction_date', $date)
            ->WhereNull('status')
            // ->where($failed)
            ->select([
                'id',
                'providers_subscription_id',
                'service_request_id',
                'transaction_id',
                'deduction_date',
                'discount',
                'status',
            ])->cursor();
        foreach ($cursor as $sub) {
            $sub->load([
                'service_request' => function ($qry) {
                    return $qry->select([
                        'id',
                        'user_id',
                        'provider_id',
                        'hours'
                    ])->with([
                        'provider' => function ($qry) {
                            return $qry->select('id')
                                ->with([
                                    'provider_profile:id,provider_id,hourly_rate'
                                ]);
                        },
                        'user:id,stripe_id'
                    ]);
                },
                'providers_subscription' => function ($qry) {
                    return $qry->select([
                        'id',
                        'user_id',
                        'off'
                    ]);
                }
            ]);
            if ($sub->service_request && $sub->service_request->status != 'REJECTED') {
                try {
                    $user = $sub->service_request->user;
                    $provider = $sub->service_request->provider;
                    $provider_profile = $provider->provider_profile;
                    $providers_subscription = $sub->providers_subscription;
                    $charge = $provider_profile->hourly_rate * $sub->service_request->hours;
                    $discount = (($charge * $providers_subscription->off) / 100);
                    $charge = $charge - $discount;
                    if ($charge > 0) {
                        $charge = $charge * 100;
                        $charge > 0 && $charge < 0.5 ? $charge = 0.5 : $charge;
                        $paymentMethod = $user->defaultPaymentMethod();
                        if ($user->stripe_id && $paymentMethod) {
                            $payment = $user->charge($charge * 100, $paymentMethod);
                            $transaction = $user->user_transactions()->create([
                                'payment_id' => $payment->id,
                                'amount' => $payment->amount / 100,
                                'amount_captured' => $payment->amount / 100,
                                'status' => $payment->status,
                                'is_credit' => 0,
                            ]);
                            $provider->provider_profile->total_earn =  intval($provider->provider_profile->earn) + $charge;
                            $commission = Commission::first();
                            $provider->provider_profile->commission = intval($provider->provider_profile->commission) + ($charge * ($commission->percentage / 100));
                            $provider->provider_profile->earn = (intval($provider->provider_profile->earn) + $charge) - $provider->provider_profile->commission;
                            $provider->push();
                            $sub->update([
                                'transaction_id' => $transaction->id,
                                'discount' => $discount,
                                'status' => 'PAID'
                            ]);
                        } else {
                            $sub->update([
                                'status' => 'FAILED',
                                'description' => 'No payment method found'
                            ]);
                            $notification = [
                                'title' => 'Payment Failed',
                                'body' => 'Payment failed for service request #' . $sub->service_request_id,
                                'type' => 'payment_failed',
                                'data' => [
                                    'service_request_id' => $sub->service_request_id,
                                    'providers_subscription_id' => $providers_subscription->id,
                                    'discount' => $discount
                                ]
                            ];
                            if ($user->device_token) Fcm::push_notification($notification, [$user->device_token], $user->role, $user->os_platform);
                            try {
                                broadcast(new AlertEvent(['id' => $user->id, 'payload' => $notification]));
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                            $user->notify(new PaymentFailedNotification($notification));
                        }
                    } else {
                        $sub->update([
                            'discount' => $discount,
                            'status' => 'PAID'
                        ]);
                    }
                } catch (\Exception $e) {
                    $sub->update([
                        'status' => 'EXCEPTION',
                        'description' => $e->getMessage()
                    ]);
                }
            }
        }
    }
}
