<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Order;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class CashfreeController extends Controller
{
    /**
     * Configure Cashfree API keys dynamically.
     */
    // public function paymentConfig()
    // {
    //     if (Auth::check()) {
    //         $payment_setting = Utility::getAdminPaymentSetting();
    //         config([
    //             'services.cashfree.key'       => $payment_setting['cashfree_api_key'] ?? '',
    //             'services.cashfree.secret'    => $payment_setting['cashfree_secret_key'] ?? '',
    //             'services.cashfree.currency'  => $payment_setting['currency'] ?? 'INR',
    //             'services.cashfree.base_url'  => config('services.cashfree.subscription_url') ?? 'https://sandbox.cashfree.com',
    //         ]);
    //     }
    // }
    public function paymentConfig()
    {
        if (Auth::check()) {
            $payment_setting = Utility::getAdminPaymentSetting();
    
            $mode = $payment_setting['cashfree_mode'] ?? 'sandbox';
            $cashfreeConfig = config("services.cashfree.$mode");
    
            config([
                'services.cashfree.key' => $payment_setting['cashfree_api_key'] ?? $cashfreeConfig['key'],
                'services.cashfree.secret' => $payment_setting['cashfree_secret_key'] ?? $cashfreeConfig['secret'],
                'services.cashfree.currency' => $payment_setting['currency'] ?? 'INR',
                'services.cashfree.base_url' => $cashfreeConfig['base_url'],
                'services.cashfree.api_url' => $cashfreeConfig['api_url'],
            ]);
        }
    }


    /**
     * Start subscription process
     */
    public function startSubscription(Request $request)
    {
        try {
            
            $this->paymentConfig();
    
            $planID = Crypt::decrypt($request->plan_id);
            $plan = Plan::findOrFail($planID);
            $user = Auth::user();
    
            $initialAmount = $plan->getTotalPlanAmount($plan->id);
    
            // if (!$plan->cashfree_plan_id) {
                $plan->cashfree_plan_id = $this->createCashfreePlan($plan);
                $plan->save();
            // }
    
            return $this->createSubscription($plan, $user, $initialAmount);
        } catch (\Exception $e) {
            Log::error('Cashfree: Start subscription failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);
            return redirect()->route('general.plans.index')
                ->with('error', 'Something went wrong while starting your subscription. Please try again or contact support.');
        }
    }

    /**
     * Create a recurring plan in Cashfree
     */
    public function createCashfreePlan(Plan $plan, $additional_license = 0)
    {
        $this->paymentConfig();
        $baseUrl = config('services.cashfree.base_url');

        if ($plan->duration === 'month') {
            $period = 1;
            $intervalUnit = "MONTH";
        } elseif ($plan->duration === '3month') {
            $period = 3;
            $intervalUnit = "MONTH";
        } elseif ($plan->duration === 'day') {
            $period = 1;
            $intervalUnit = "DAY";
        } else {
            $period = 1;
            $intervalUnit = "YEAR";
        }
        
        if($additional_license <= 0) {
            $planAmount = $plan->getTotalPlanAmount($plan->id);
        } else {
            $planAmount = $plan->getTotalPlanAmountWithAdditionalUsers($additional_license);
        }

        $payload = [
            "plan_id"               => 'plan_' . $plan->id . '_' . uniqid(),
            "plan_name"             => $plan->name,
            "plan_type"             => "PERIODIC",
            "plan_currency"         => config('services.cashfree.currency'),
            "plan_status"           => "ACTIVE",
            "plan_max_amount"       => (float)$planAmount,
            "plan_intervals"        => $period,
            "plan_interval_type"    => $intervalUnit,
            "plan_recurring_amount" => (float)$planAmount,
        ];

        $response = Http::withHeaders([
            "x-client-id"     => config('services.cashfree.key'),
            "x-client-secret" => config('services.cashfree.secret'),
            "x-api-version"   => "2025-01-01",
            "Content-Type"    => "application/json",
        ])->post("{$baseUrl}/pg/plans", $payload);
        Log::info(config('services.cashfree.key'));
        Log::info(config('services.cashfree.secret'));
        Log::info($baseUrl);

        if ($response->successful()) {
            Log::info('Cashfree: Created plan successfully', $response->json());
            return $response->json()['plan_id'];
        }

        Log::error('Cashfree: Failed to create plan', ['response' => $response->json()]);
        throw new \Exception('Unable to create Cashfree plan.');
    }

    /**
     * Create a subscription with immediate charge
     */
    public function createSubscription(Plan $plan, $user, $amount)
    {
        $this->paymentConfig();
        $baseUrl = config('services.cashfree.base_url');

        $subscriptionId = 'sub_' . $user->id . '_' . time();

        $returnUrl = route('cashfreePayment.success') . '?' . http_build_query([
            'plan_id'           => $plan->id,
            'previous_subscription_id' => $user->cashfree_subscription_id
        ]);

        $payload = [
            'subscription_id'       => $subscriptionId,
            // "next_schedule_date" => "2025-09-07",
            'plan_details'          => [
                'plan_id' => $plan->cashfree_plan_id,
            ],
            'customer_details'      => [
                'customer_id'    => 'cust_' . $user->id,
                'customer_name'  => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->mobile_no,
            ],
            'authorization_details' => [
                'authorization_amount' => (float)$amount,
            ],
            'subscription_meta'     => [
                'return_url'           => $returnUrl,
                'notification_channel' => ['EMAIL', 'SMS'],
            ],
        ];

        $response = Http::withHeaders([
            "x-client-id"     => config('services.cashfree.key'),
            "x-client-secret" => config('services.cashfree.secret'),
            "x-api-version"   => "2025-01-01",
            "Content-Type"    => "application/json",
        ])->post("{$baseUrl}/pg/subscriptions", $payload);

        $json = $response->json();
        Log::info('Cashfree: Created subscription response', $json);

        if ($response->successful() && isset($json['subscription_session_id'], $json['subscription_id'])) {
            $user->cashfree_subscription_id = $json['subscription_id'];
            $user->save();

            return view('shared.subscription.cashfree', [
                'sessionId' => $json['subscription_session_id'],
            ]);
        }

        Log::error('Cashfree: Failed to create subscription', ['response' => $json, 'payload' => $payload]);
        return redirect()->route('general.plans.index')->with('error', 'Unable to create subscription. Please try again.');
    }

    /**
     * Cancel all old subscriptions except the new one
     */
    public function cancelSubscription($user, $subscriptionId = null)
    {
        $this->paymentConfig();
        $baseUrl = config('services.cashfree.base_url');

        if (empty($subscriptionId)) {
            return;
        }
        
        $details = $this->getSubscriptionDetails($subscriptionId);

        if (!isset($details['subscription_status'])) {
            return;
        }

        if ($details['subscription_status'] === 'ACTIVE' || $details['subscription_status'] === 'BANK_APPROVAL_PENDING') {
            $cancelResponse = Http::withHeaders([
                "x-client-id"     => config('services.cashfree.key'),
                "x-client-secret" => config('services.cashfree.secret'),
                "x-api-version"   => "2025-01-01",
                "Content-Type"    => "application/json",
            ])->post("{$baseUrl}/pg/subscriptions/{$details['subscription_id']}/manage", [
                'action' => 'CANCEL'
            ]);

            if ($cancelResponse->successful()) {
                Log::info('Cashfree: Successfully cancelled old subscription', [
                    'user_id' => $user->id,
                    'subscription_id' => $details['subscription_id']
                ]);
            } else {
                Log::error('Cashfree: Failed to cancel old subscription', [
                    'user_id' => $user->id,
                    'subscription_id' => $details['subscription_id'],
                    'response' => $cancelResponse->json()
                ]);
            }
        }

    }

    /**
     * Payment success callback
     */
    public function cashfreePaymentSuccess(Request $request)
    {
        \Log::info('Cashfree: Subscription Success Response', $request->all());
        
        $this->paymentConfig();
        $user = Auth::user();
        if(empty($user)) {
            $user = User::where('cashfree_subscription_id', $request->cf_subscriptionId)->first();
            \Auth::login($user);
            $this->paymentConfig();
            
        }
        $plan = Plan::findOrFail($request->plan_id);
        
        $details = $this->getSubscriptionDetails($user->cashfree_subscription_id);

        if (
            empty($user) || 
            !isset($details['subscription_status']) || 
            ($details['subscription_status'] !== 'ACTIVE' && $details['subscription_status'] !== 'BANK_APPROVAL_PENDING') ||
            empty($request->cf_subscriptionPaymentId)
        ) {
            return redirect()->route('general.plans.index')->with('error', 'Subscription activation failed.');
        }
        
        $this->cancelSubscription($user, $request->previous_subscription_id);

        $oldOrder = Order::where('order_id', $request->cf_subscriptionPaymentId)->where('user_id', $user->id)->first();
        if(empty($oldOrder)) {
            $order = new Order();
            $order->order_id = uniqid('order_');
            $order->user_id = $user->id;
            $order->plan_id = $plan->id;
            $order->price = $request->cf_authAmount;
            $order->price_currency = config('services.cashfree.currency');
            $order->payment_status = 'success';
            $order->payment_type = 'Cashfree';
            
            $order->save();
            $oldOrder = $order;
            
        }
        
        if($request->has('additional_license')) {
            $plan->max_users += $request->additional_license;
            $plan->save();
            
            $oldOrder->payment_type = "Cashfree (License Upgrade)";
            $oldOrder->save();
            
            return redirect()->route('general.plans.index')->with('success', 'License upgraded successfully and recurring amount updated from next cycle.');
        }
        
        $assign = $user->assignPlan($plan->id);
        if ($assign['is_success']) {
            return redirect()->route('general.plans.index')->with('success', 'Plan activated successfully.');
        }

        return redirect()->route('general.plans.index')->with('error', $assign['error']);
    }

    /**
     * Get subscription details
     */
    public function getSubscriptionDetails($subscriptionId)
    {
        $this->paymentConfig();
        $baseUrl = config('services.cashfree.base_url');

        $response = Http::withHeaders([
            "x-client-id"     => config('services.cashfree.key'),
            "x-client-secret" => config('services.cashfree.secret'),
            "x-api-version"   => "2025-01-01",
        ])->get("{$baseUrl}/pg/subscriptions/{$subscriptionId}");

        Log::info('Cashfree: Subscription details response', $response->json());
        return $response->successful() ? $response->json() : null;
    }
    
    /**
     * Create instant checkout for immediate payment
     */
    public function createInstantCheckout(Plan $plan, $user, $additional_license)
    {
        $this->paymentConfig();
        $baseUrl = config('services.cashfree.base_url');
        
        $orderId = 'order_' . $user->id . '_' . time();
        
        $amount = $plan->getAdditionalUsersAmount($additional_license);
        
        $returnUrl = route('cashfreePayment.upgradelicense.success') . '?' . http_build_query([
            'order_id'           => $orderId,
            'additional_license' => $additional_license
        ]);
        
        $plan->cashfree_plan_id = $this->createCashfreePlan($plan, $additional_license);
        $plan->save();
        
        $subscriptionId = 'sub_' . $user->id . '_' . time();

        $returnUrl = route('cashfreePayment.success') . '?' . http_build_query([
            'plan_id'           => $plan->id,
            'previous_subscription_id' => $user->cashfree_subscription_id,
            'additional_license' => $additional_license
        ]);

        $payload = [
            'subscription_id'       => $subscriptionId,
            "next_schedule_date" => $user->plan_expire_date,
            'plan_details'          => [
                'plan_id' => $plan->cashfree_plan_id,
            ],
            'customer_details'      => [
                'customer_id'    => 'cust_' . $user->id,
                'customer_name'  => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->mobile_no,
            ],
            'authorization_details' => [
                'authorization_amount' => (float)$amount,
            ],
            'subscription_meta'     => [
                'return_url'           => $returnUrl,
                'notification_channel' => ['EMAIL', 'SMS'],
            ],
        ];

        $response = Http::withHeaders([
            "x-client-id"     => config('services.cashfree.key'),
            "x-client-secret" => config('services.cashfree.secret'),
            "x-api-version"   => "2025-01-01",
            "Content-Type"    => "application/json",
        ])->post("{$baseUrl}/pg/subscriptions", $payload);

        $json = $response->json();
        Log::info('Cashfree: Create subscription response', $json);

        if ($response->successful() && isset($json['subscription_session_id'], $json['subscription_id'])) {
            $user->cashfree_subscription_id = $json['subscription_id'];
            $user->save();

            return view('shared.subscription.cashfree', [
                'sessionId' => $json['subscription_session_id'],
            ]);
        }
        
        return back()->with('error', 'Try again');
    }
    
    public function upgradeLicensesSuccess(Request $request)
    {
        \Log::info($request->all());
    
        $this->paymentConfig();
        $baseUrl = config('services.cashfree.base_url');
        $user = Auth::user();
        $plan = Plan::findOrFail($user->plan);
        $oldSubscriptionId = $user->cashfree_subscription_id;
    
        try {
            $additional_license = $request->input('additional_license');
            $orderId = $request->input('order_id');
    
            $response = Http::withHeaders([
                "x-client-id"     => config('services.cashfree.key'),
                "x-client-secret" => config('services.cashfree.secret'),
                "x-api-version"   => "2023-08-01",
                "Content-Type"    => "application/json",
            ])->get("{$baseUrl}/pg/orders/{$orderId}");
    
            $json = $response->json();
    
            Log::info('Cashfree: Order status', [
                'response' => $json,
                'user_id'  => $user->id
            ]);
    
            if ($response->failed() || $json['order_status'] !== 'PAID') {
                return redirect()->route('general.plans.index')->with('error', 'Payment not successful. Please try again.');
            }
    
            $extraAmount = $json['order_amount'];
            $currency    = $json['order_currency'];
    
            $subResponse = Http::withHeaders([
                "x-client-id"     => config('services.cashfree.key'),
                "x-client-secret" => config('services.cashfree.secret'),
                "x-api-version"   => "2025-01-01",
                "Content-Type"    => "application/json",
            ])->get("{$baseUrl}/pg/subscriptions/{$oldSubscriptionId}");
    
            $subJson = $subResponse->json();
            \Log::info('Cashfree: Old subscription details', $subJson);
            $nextScheduleDate = $subJson['next_schedule_date'] ?? now()->addDay()->startOfDay()->toIso8601String();
            
            Http::withHeaders([
                "x-client-id"     => config('services.cashfree.key'),
                "x-client-secret" => config('services.cashfree.secret'),
                "x-api-version"   => "2025-01-01",
                "Content-Type"    => "application/json",
            ])->post("{$baseUrl}/pg/subscriptions/{$oldSubscriptionId}/manage", [
                'subscription_id' => $oldSubscriptionId,
                'action'          => 'CANCEL',
                'action_details'  => ['cancel_reason' => 'Upgrading plan with extra licenses']
            ]);
    
            // $newPlanId = $this->createCashfreePlan($plan, $additional_license);
    
            // $newSubPayload = [
            //     'subscription_id'       => 'sub_' . $user->id . '_' . time(),
            //     'plan_details'          => [
            //         'plan_id' => $newPlanId,
            //     ],
            //     'customer_details'      => [
            //         'customer_id'    => 'cust_' . $user->id,
            //         'customer_name'  => $user->name,
            //         'customer_email' => $user->email,
            //         'customer_phone' => $user->mobile_no,
            //     ],
            //     'subscription_first_charge_time'     => $nextScheduleDate,
            // ];
    
            // $newSubResponse = Http::withHeaders([
            //     "x-client-id"     => config('services.cashfree.key'),
            //     "x-client-secret" => config('services.cashfree.secret'),
            //     "x-api-version"   => "2025-01-01",
            //     "Content-Type"    => "application/json",
            // ])->post("{$baseUrl}/pg/subscriptions", $newSubPayload);
    
            // $newSubJson = $newSubResponse->json();
            // Log::info('Cashfree: New subscription created', $newSubJson);
    
            // if ($newSubResponse->failed()) {
            //     return redirect()->route('general.plans.index')->with('error', 'Failed to create new subscription. Contact support.');
            // }
    
            // $user->cashfree_subscription_id = $newSubJson['subscription_id'];
            // $user->save();
    
            $plan->max_users += $additional_license;
            $plan->save();
    
            $order = new Order();
            $order->order_id        = $orderId;
            $order->user_id         = $user->id;
            $order->plan_id         = $plan->id;
            $order->price           = $extraAmount;
            $order->price_currency  = $currency;
            $order->payment_status  = 'success';
            $order->payment_type    = 'Cashfree';
            $order->save();
    
            return redirect()->route('general.plans.index')->with('success', 'License upgraded successfully and recurring amount updated from next cycle.');
    
        } catch (\Exception $e) {
            Log::error('Cashfree: License upgrade error', [
                'error'   => $e->getMessage(),
                'user_id' => $user->id
            ]);
            return redirect()->route('general.plans.index')->with('error', 'An unexpected error occurred during license upgrade.');
        }
    }

    
    public function handleWebhook(Request $request)
    {

    
        // $signature = $request->header('x-webhook-signature');
        // if (!$this->verifySignature($request->getContent(), $signature)) {
        //     Log::error('Cashfree: Invalid webhook signature');
        //     return response()->json(['error' => 'Invalid signature'], 400);
        // }
    
        $event = $request->input('type');
        $data  = $request->input('data');
        Log::info('Cashfree Webhook Received', ['event' => $event]);
        Log::info('Cashfree Webhook Data', is_array($data) ? $data : ['data' => $data]);
    
        if (!$event || !$data) {
            Log::warning('Cashfree: Webhook missing event/data');
            return response()->json(['error' => 'Invalid payload'], 400);
        }
    
        try {
            switch ($event) {
    
                case 'SUBSCRIPTION_PAYMENT_SUCCESS':
                    if (($data['payment_status'] ?? null) === 'SUCCESS') {
                        $this->handleRecurringCharge($data);
                    } else {
                        Log::warning("Cashfree: Subscription payment not successful");
                    }
                    break;
    
                default:
                    Log::info("Cashfree: Unhandled webhook event {$event}");
            }
        } catch (\Exception $e) {
            Log::error('Cashfree: Webhook handler error', [
                'event' => $event,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Verify webhook signature
     */
    private function verifySignature($payload, $signature)
    {
        $secret = env('CASHFREE_WEBHOOK_SECRET'); // set in .env
        $computed = hash_hmac('sha256', $payload, $secret);
        return hash_equals($computed, $signature);
    }

    /**
     * Handle recurring charge success
     */
    private function handleRecurringCharge($data)
    {
        $subscriptionId = $data['subscription_id'];
        $amount = $data['payment_amount'];
        $currency = $data['payment_currency'];
    
        $user = User::where('cashfree_subscription_id', $subscriptionId)->first();
        
        $plan = Plan::findOrFail($user->plan);
        if ($user && $plan) {
    
            $order = new Order();
            $order->order_id = $data['cf_payment_id']??$data['payment_id'];
            $order->user_id = $user->id;
            $order->plan_id = $user->plan;
            $order->price = $amount;
            $order->price_currency = $currency;
            $order->payment_status = 'success';
            $order->payment_type = 'Cashfree Recurring';
            $order->save();
            
            $assign = $user->assignPlan($plan->id);
    
            Log::info("Cashfree: Recurring charge success for user {$user->id}");
        }
    }

}
