<?php

namespace App\Http\Helpers;

use Stripe\Charge;
use Stripe\Stripe;
use App\Models\User;
use Stripe\Customer;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\CardException;
use Illuminate\Support\Facades\Storage;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\RateLimitException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\InvalidRequestException;

class Common
{
    public static function send_sms($phone, $message)
    {

        try {
            // $curl = curl_init();
            $api_key = config('services.nexmo_api');
            $api_secret = config('services.nexmo_api_secret');

            $url = 'https://rest.nexmo.com/sms/json?' . http_build_query([
                    'api_key' => $api_key,
                    'api_secret' => $api_secret,
                    'to' => $phone,
                    'from' => "12013400702",
                    'text' => $message
                ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);

            // curl_setopt_array($curl, array(
            // 	CURLOPT_URL => "https://rest.nexmo.com/sms/json?from=+12013400702&text=asad&to=".$phone."&api_key=".$api_key."&api_secret=#Y25X3csNqAD",
            // 	CURLOPT_RETURNTRANSFER => true,
            // 	CURLOPT_ENCODING => '',
            // 	CURLOPT_MAXREDIRS => 10,
            // 	CURLOPT_TIMEOUT => 0,
            // 	CURLOPT_FOLLOWLOCATION => true,
            // 	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // 	CURLOPT_CUSTOMREQUEST => 'POST',
            // ));

            // $response = curl_exec($curl);

            // curl_close($curl);
            $response = json_decode($response, true);
            if (isset($response['messages'][0]['status']) && $response['messages'][0]['status'] == 0) {
                return ['error' => false, 'message' => 'success'];
            } else {
                return ['error' => true, 'message' => 'Failed'];
            }
        } catch (\Throwable $th) {
            throw $th;
        }


        // $accountSid = env('TWILIO_SID');
        // $authToken = env('TWILIO_AUTH_TOKEN');
        // $twilioNumber = env('TWILIO_FROM_PHONE');

        // $client = new TwilioClient($accountSid, $authToken);
        // try {
        // 	$client->messages->create($phone, ["body" => $message, "from" => $twilioNumber]);
        // 	return ['error' => false, 'message' => 'success'];
        // } catch (TwilioException $e) {
        // 	Log::error(['Common -> send_sms', $e->getMessage()]);
        // 	return ['error' => true, 'message' => $e->getMessage()];
        // }
    }

    public static function stripe_payment($token = null, $amount, $description)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $user = auth()->user();
            if ($token == null && $user->stripe_id && $user->defaultPaymentMethod()) {
                $source = ['customer' => $user->stripe_id];
            } else {
                $source = ["source" => $token];
            }
            if (isset($source) && $source !== null) {
                $data = Charge::create([
                        "amount" => $amount * 100,
                        "currency" => "usd",
                        "description" => $description
                    ] + $source);
                if ($data->status === "succeeded") {
                    return ['error' => false, 'data' => $data];
                } else {
                    return ['error' => true, 'data' => $data];
                }
            }
            return ['error' => true, 'data' => 'Invalid payment method'];
        } catch (CardException $cardExp) {
            return ['error' => true, 'data' => $cardExp->getMessage()];
        } catch (RateLimitException  $rateExp) {
            return ['error' => true, 'data' => $rateExp->getMessage()];
        } catch (InvalidRequestException   $invalidExp) {
            return ['error' => true, 'data' => $invalidExp->getMessage()];
        } catch (AuthenticationException   $authExp) {
            return ['error' => true, 'data' => $authExp->getMessage()];
        } catch (ApiConnectionException   $apiConnectionExp) {
            return ['error' => true, 'data' => $apiConnectionExp->getMessage()];
        } catch (ApiErrorException    $apiErrorExp) {
            return ['error' => true, 'data' => $apiErrorExp->getMessage()];
        } catch (\Exception $e) {
            return ['error' => true, 'data' => $e->getMessage()];
        }
    }

    /**
     * add card to stripe
     */
    public static function stripe_add_card($token, $fingerprint = null)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $user = auth()->user();
            $user->createOrGetStripeCustomer();
            // $data = Customer::allSources($customer_id);
            // if ($data && isset($data->data) && $fingerprint != null) {
            // 	foreach ($data->data as $key => $value) {
            // 		if ($value->fingerprint === $fingerprint) {
            // 			return ['error' => false, 'data' => $value];
            // 		}
            // 	}
            // }
            $card = Customer::createSource($user->stripe_id, ['source' => $token]);
            if (isset($card)) {
                return ['error' => false, 'data' => $card];
            } else {
                return ['error' => true, 'data' => $card];
            }
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                $e->getMessage(),
                $e->getLine(),
                $e->getFile()
            ]);
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * update_default_card
     * @param mixed $card_id
     * @param mixed $user
     * @return bool
     */
    public static function update_default_card($card_id, $user = null)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            if (!$user || !$user->stripe_id) {
                $user = auth()->user();
                $user->createOrGetStripeCustomer();
            }
            Customer::update($user->stripe_id, [
                'default_source' => $card_id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                $e->getMessage(),
                $e->getLine(),
                $e->getFile()
            ]);
            return false;
        }
    }

    /**
     * store media in storage
     * @param object $media
     * @param string $path
     * @param string $name
     */
    public function store_media($media, $path, $name = null)
    {
        try {
            $path = '/public/' . $path;
            $name = $name ? $name : uniqid() . '-' . time();
            $name = $name . "." . $media->getClientOriginalExtension();
            if (Storage::exists($path)) {
                return Storage::url($media->storeAs($path, $name));
            } else {
                Storage::makeDirectory($path);
                return Storage::url($media->storeAs($path, $name));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * delete media from storage
     */
    public function delete_media($path)
    {
        try {
            $path = str_replace('/storage', '/public', $path);
            if (Storage::exists($path)) {
                return Storage::delete($path);
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function defaultCurrencySymbol()
    {
        return request()->default_country?->currency_symbol ?? '$';
    }

}
