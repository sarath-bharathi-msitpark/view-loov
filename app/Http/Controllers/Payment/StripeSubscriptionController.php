<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Subscription;
use Stripe\Invoice;
use Stripe\Webhook;
use App\Models\Utility;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\User;
use App\Models\Order;

class StripeSubscriptionController extends Controller
{
    public function __construct()
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        Stripe::setApiKey($admin_payment_setting['stripe_secret']);
    }

    // Show checkout page
    public function showCheckout()
    {
        return view('shared.subscription.checkout');
    }

    public function createCheckoutSession(Request $request)
    {
        $planID = Crypt::decrypt($request->plan_id);
        $plan = Plan::findOrFail($planID);
        $user = \Auth::user();

        try {
        if (!$user->stripe_customer_id) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'address.line1'       => 'required|string|max:255',
                    'address.city'        => 'required|string|max:255',
                    'address.postal_code' => 'required|string|max:50',
                    'address.state'       => 'nullable|string|max:255',
                    'address.country'     => 'required|string|size:2',
                    'mode'                => 'nullable|in:trial',
                ]
            );
            
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            
            $validated = $validator->validated();
            
            $user->update([
                'address' => $validated['address']['line1'],
                'city'        => $validated['address']['city'],
                'postal_code' => $validated['address']['postal_code'],
                'state'       => $validated['address']['state'] ?? null,
                'country'     => strtoupper($validated['address']['country']),
            ]);
            $customer = \Stripe\Customer::create([
                'name'    => $user->name,
                'email'   => $user->email,
                'address' => [
                    'line1'       => $validated['address']['line1'],
                    'city'        => $validated['address']['city'],
                    'postal_code' => $validated['address']['postal_code'],
                    'state'       => $validated['address']['state'] ?? null,
                    'country'     => strtoupper($validated['address']['country']),
                ],
            ]);
        
            $user->stripe_customer_id = $customer->id;
            $user->save();
        } else {
            $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);
        }

        $interval = match ($plan->duration) {
            'lifetime' => null,
            'month'    => 'month',
            '3month'   => 'month',
            'year'     => 'year',
            default    => 'month',
        };

        $intervalCount = ($plan->duration === '3month') ? 3 : 1;

        $total = round($plan->getSingleUserPlanAmount() * 100);
        $lineItem = [
            'price_data' => [
                'currency'    => 'inr',
                'unit_amount' => 5000,
                'product_data'=> [
                    'name'        => $plan->name,
                    'description' => $plan->description,
                ],
            ],
            'quantity' => $plan->max_users,
        ];
        
        if ($interval) {
            $lineItem['price_data']['recurring'] = [
                'interval'       => $interval,
                'interval_count' => $intervalCount,
            ];
        }

        $subscriptionData = [
            'metadata' => [
                'plan_id' => $plan->id,
                'user_id' => $user->id,
            ],
        ];
        if (!empty(\Auth::user()->plan) && isset($validated['mode']) && $validated['mode'] === 'trial' && $interval) {
            $subscriptionData['trial_period_days'] = $plan->trial_days ?? 7;
        }

        $sessionData = [
            'payment_method_types' => ['card'],
            'customer'             => $customer->id,
            'line_items'           => [$lineItem],
            'allow_promotion_codes'=> true,
            'success_url'          => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('stripe.cancel'),
            'metadata'             => ['plan_id' => $plan->id, 'user_id' => $user->id]
        ];
        if ($interval) {
            $sessionData['mode'] = 'subscription';
            $sessionData['subscription_data'] = $subscriptionData;
        } else {
            $sessionData['mode'] = 'payment';
        }

        $session = \Stripe\Checkout\Session::create($sessionData);
    \Log::info("Stripe session created", $session->toArray());
        return redirect($session->url);
        } catch (\Stripe\Exception\ApiErrorException $e) {
        // Stripe specific error
        
        dd($e->getMessage());
        \Log::error("Stripe API error: " . $e->getMessage());
        return back()->with('error', 'Stripe error: ' . $e->getMessage());
    } catch (\Exception $e) {
        dd($e->getMessage());
        \Log::error("Checkout error: " . $e->getMessage());
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
    }

    public function cancelSubscription(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => 'required|string',
            'at_period_end'   => 'nullable|boolean',
        ]);

        $subscription = Subscription::retrieve($validated['subscription_id']);

        if ($request->boolean('at_period_end')) {
            $subscription->cancel_at_period_end = true;
            $subscription->save();
        } else {
            $subscription->cancel();
        }

        return back()->with('status', 'Subscription cancelled!');
    }

    public function addLicense(Request $request)
    {
        $validated = $request->validate([
            'plan_id'           => 'required|string',
            'additional_license'=> 'required|integer|min:1',
        ]);
    
        $planID = Crypt::decrypt($validated['plan_id']);
        $plan   = Plan::findOrFail($planID);
        $user   = auth()->user();
        
        // if ($user->max_users <= $validated['additional_license']) {
        //     return back()->with('error', 'Cannot decrease licenses here. Please contact support.');
        // }
        if ($user->payment_mode === "cashfree" && empty($user->cashfree_subscription_id)) {
            return back()->with('error', 'No active subscription found.');
        }else if ($user->payment_mode === "stripe" && empty($user->stripe_subscription_id)) {
            return back()->with('error', 'No active subscription found.');
        }
        
    
        // DB::beginTransaction();
    
        try {
            
            if (!empty($user->cashfree_subscription_id) && $user->payment_mode === "cashfree") {
                return app(\App\Http\Controllers\Payment\CashfreeController::class)->createInstantCheckout($plan, $user, $validated['additional_license']);
            }
            
            $subscription = Subscription::retrieve($user->stripe_subscription_id);
            $subscriptionItem = $subscription->items->data[0];
    
            $newQty = $plan->max_users + $validated['additional_license'];
    
            // if ($newQty <= $subscriptionItem->quantity) {
            //     return back()->with('error', 'Cannot decrease licenses here. Please contact support.');
            // }
    
            Subscription::update($subscription->id, [
                'items' => [[
                    'id'       => $subscriptionItem->id,
                    'quantity' => $newQty,
                ]],
                'proration_behavior'   => 'create_prorations',
                // 'billing_cycle_anchor' => 'unchanged',
            ]);
    
            // $invoice = Invoice::upcoming([
            //     'customer'     => $subscription->customer,
            //     'subscription' => $subscription->id,
            // ]);
    
            // if ($invoice) {
            //     Invoice::create([
            //         'customer'     => $subscription->customer,
            //         'subscription' => $subscription->id,
            //         'auto_advance' => true,
            //     ]);
            // }
    
            // $plan->max_users = $newQty;
            // $plan->save();
            
            $invoice = Invoice::create([
                'customer'     => $subscription->customer,
                'subscription' => $subscription->id,
                'auto_advance' => true,
                'metadata'     => [
                    'action' => 'license_update',
                    'user_id' => $user->id,
                ]
            ]);
            
            $invoice = $invoice->finalizeInvoice();

            $invoice->pay();
    
            // DB::commit();
    
            return back()->with('success', 'License request submitted. Changes will apply once payment is confirmed.');
    
        } catch (\Exception $e) {
            // DB::rollBack();
            // dd($e->getMessage());
            \Log::error('License addition failed: '.$e->getMessage());
            return back()->with('error', 'Failed to add license. Please try again.');
        }
    }

    public function adminReduceLicense(User $user, int $newQty)
    {
        if (!$user->stripe_subscription_id) {
            return back()->with('error', 'User has no active subscription.');
        }

        $subscription = Subscription::retrieve($user->stripe_subscription_id);
        $subscriptionItem = $subscription->items->data[0];

        if ($newQty >= $subscriptionItem->quantity) {
            return back()->with('error', 'New license count must be less than current quantity.');
        }

        Subscription::update($subscription->id, [
            'items' => [[
                'id'       => $subscriptionItem->id,
                'quantity' => $newQty,
            ]],
            'proration_behavior' => 'none', // no immediate refund
        ]);

        $user->max_users = $newQty;
        $user->save();

        return back()->with('status', 'User licenses reduced successfully.');
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            abort(404);
        }
    
        // At this point, the webhook will handle plan assignment and order creation
        return redirect()->route('general.plans.index')
                         ->with('success', __('Payment successful! Your subscription will be activated shortly.'));
    }
    
    public function cancel()
    {
        return redirect()->route('general.plans.index')
                         ->with('error', __('Payment was cancelled. No changes were made to your subscription.'));
    }

    public function webhookHandle(Request $request)
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid payload or signature'], 400);
        }
        
            \Log::info("Stripe webhook received", ['id' => $event->id, 'type' => $event->type]);

        switch ($event->type) {

            case 'checkout.session.completed':
                $session = $event->data->object;
                $metadata = $session->metadata;
                $user = User::find($metadata['user_id'] ?? null);
                $plan = Plan::find($metadata['plan_id'] ?? null);
                
                if (!$user || !$plan) break;
                
                $subscriptionId = $session->subscription ?? null;

                $isTrial = false;
                if ($subscriptionId) {
                    $subscription = Subscription::retrieve($subscriptionId);
                    if (isset($subscription->trial_end) && $subscription->trial_end > $subscription->current_period_start) {
                        $isTrial = true;
                    }
                }
                
                if ($user->stripe_subscription_id && $user->stripe_subscription_id !== $subscriptionId) {
                    try {
                        $oldSub = Subscription::retrieve($user->stripe_subscription_id);
                        $oldSub->cancel();
                        \Log::info("Subscription cancelled");
                    } catch (\Exception $e) {
                        // ignore if already canceled
                    }
                }
    
                if ($isTrial) {
                    $user->plan = $plan->id;
                    $user->stripe_subscription_id = $subscriptionId;
                    $user->save();
                    $user->assignPlan($plan->id);
                    Order::create([
                        'order_id' => strtoupper(str_replace('.', '', uniqid('', true))),
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'plan_name' => $plan->name,
                        'price' => 0,
                        'payment_status' => 'success',
                        'payment_type' => 'Stripe',
                    ]);
                }

                break;

            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                // \Log::info("Invoice payment succeeded", ['invoice' => $invoice]);
            
                try {
                    $metadata = (array)($invoice->metadata ?? []);
                    $userId   = $metadata['user_id'] ?? null;
                    $planId   = $metadata['plan_id'] ?? null;
            
                    $user = null;
                    if ($userId) {
                        $user = User::find($userId);
                    }
                    if (!$user && isset($invoice->customer)) {
                        $user = User::where('stripe_customer_id', $invoice->customer)->first();
                    }
                    if (!$user) {
                        \Log::warning("No user found for invoice", [
                            'invoice_id' => $invoice->id,
                            'customer'   => $invoice->customer,
                            'user_id'    => $userId,
                        ]);
                        break;
                    }
                    
                    $subscriptionId = $invoice->subscription
                        ?? ($invoice->parent->subscription_details->subscription ?? null)
                        ?? ($invoice->lines->data[0]->parent->subscription_item_details->subscription ?? null);
                    
                    if (empty($subscriptionId)) {
                        \Log::warning("Invoice has no subscription (checked all sources), skipping cancellation", [
                            'invoice_id' => $invoice->id,
                            'user_id'    => $user->id,
                        ]);
                        break;
                    }
                    $subscription   = $subscriptionId ? \Stripe\Subscription::retrieve($subscriptionId) : null;
                    $subscriptionItem = $subscription && isset($subscription->items->data[0])
                        ? $subscription->items->data[0]
                        : null;
                    
                    if ($invoice->billing_reason === 'subscription_create' && $invoice->amount_paid == 0) {
                        \Log::info("Skipping trial invoice", ['invoice_id' => $invoice->id, 'user_id' => $user->id]);
                        break;
                    }
                    
                    if ($invoice->billing_reason === 'manual' && ($invoice->metadata->action ?? null) === 'license_update') {
                
                        if ($subscription && $user) {
                            $subscriptionItem = $subscription->items->data[0];
                            $newQty = $subscriptionItem->quantity;
                
                            $plan = Plan::find($user->plan);
                            if ($plan) {
                                $plan->max_users = $newQty;
                                $plan->save();
                            }
                
                            \Log::info("Manual invoice handled: license count updated", [
                                'invoice_id' => $invoice->id,
                                'user_id'    => $user->id,
                                'new_qty'    => $newQty,
                            ]);
                            
                            Order::create([
                                'order_id'     => strtoupper(str_replace('.', '', uniqid('', true))),
                                'user_id'      => $user->id,
                                'plan_id'      => $plan->id,
                                'amount'       => $invoice->amount_paid / 100,
                                'currency'     => strtoupper($invoice->currency),
                                'txn_id'       => $invoice->id,
                                'payment_type' => 'Stripe',
                                'payment_status' => 'success',
                                'receipt' => $invoice->invoice_pdf
                            ]);
                        }
                
                        break;
                    }

            
                    $plan = null;
                    if ($planId) {
                        $plan = Plan::find($planId);
                    }
                    if (!$plan && isset($invoice->lines->data[0]->metadata->plan_id)) {
                        $plan = Plan::find($invoice->lines->data[0]->metadata->plan_id);
                    }
                    if (!$plan) {
                        \Log::warning("No plan found for invoice", [
                            'invoice_id' => $invoice->id,
                            'plan_id'    => $planId,
                        ]);
                        break;
                    }
                    
                    DB::beginTransaction();
            
                    $user->plan = $plan->id;
                    $user->stripe_subscription_id = $subscriptionId ?? null;
                    $user->save();
                    
                    if ($subscriptionItem) {
                        $plan->max_users = $subscriptionItem->quantity;
                        $plan->save();
                    }
            
                    $user->assignPlan($plan->id);
            
                    Order::create([
                        'order_id'     => strtoupper(str_replace('.', '', uniqid('', true))),
                        'user_id'      => $user->id,
                        'plan_id'      => $plan->id,
                        'amount'       => $invoice->amount_paid / 100, // convert from cents
                        'currency'     => strtoupper($invoice->currency),
                        'txn_id'       => $invoice->id,
                        'payment_type' => 'Stripe',
                        'payment_status' => 'success',
                        'receipt' => $invoice->invoice_pdf
                    ]);
            
                    DB::commit();
            
                    \Log::info("Invoice handled successfully", [
                        'invoice_id' => $invoice->id,
                        'user_id'    => $user->id,
                        'plan_id'    => $plan->id,
                    ]);
            
                    if ($user->stripe_customer_id) {
                        $subscriptions = \Stripe\Subscription::all([
                            'customer' => $user->stripe_customer_id,
                            'status'   => 'active',
                            'limit'    => 100,
                        ]);
                    
                        foreach ($subscriptions->data as $sub) {
                            if ($sub->id !== $user->stripe_subscription_id) {
                                try {
                                    $sub->cancel(); // cancels immediately
                                    \Log::info("Cancelled old subscription", [
                                        'user_id' => $user->id,
                                        'sub_id'  => $sub->id,
                                    ]);
                                } catch (\Exception $e) {
                                    \Log::warning("Could not cancel subscription", [
                                        'user_id' => $user->id,
                                        'sub_id'  => $sub->id,
                                        'error'   => $e->getMessage(),
                                    ]);
                                }
                            }
                        }
                    }
            
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error("Failed to handle invoice", [
                        'invoice_id' => $invoice->id ?? null,
                        'error'      => $e->getMessage(),
                        'trace'      => $e->getTraceAsString(),
                    ]);
                }
            
                break;


            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                // \Log::info("Subscription deleted", ['subscription' => $subscription]);
            
                try {
                    $customerId     = $subscription->customer ?? null;
                    $subscriptionId = $subscription->id ?? null;
            
                    if (!$customerId || !$subscriptionId) {
                        break;
                    }
            
                    $user = User::where('stripe_customer_id', $customerId)->first();
            
                    if (!$user) {
                        break;
                    }
            
                    if ($user->stripe_subscription_id === $subscriptionId) {
                        $sub = \Stripe\Subscription::retrieve($subscriptionId);
                        if ($sub->status === 'canceled') {
                            $user->stripe_subscription_id = null;
                            $user->save();
                            \Log::info("User subscription cleared", ['user_id' => $user->id]);
                        }
                    }
                } catch (\Exception $e) {
                }
            
                break;

        }

        return response()->json(['status' => 'success'], 200);
    }
}
