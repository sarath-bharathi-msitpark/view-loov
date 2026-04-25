<?php

namespace App\Http\Controllers\Web\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Subscription;
use App\Models\Utility;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $admin_payment_setting = Utility::getAdminPaymentSetting();
        Stripe::setApiKey($admin_payment_setting['stripe_secret']);
    }

    public function showCheckout()
    {
        return view('shared.subscription.checkout');
    }

    /**
     * Create Checkout Session
     * - mode: trial | immediate
     * - supports license count
     */
    public function createCheckoutSession(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email',
            'amount' => 'required|numeric|min:0.5',   // price per license
            'licenses' => 'required|integer|min:1',
            'address.line1'      => 'required|string|max:255',
            'address.city'       => 'required|string|max:255',
            'address.postal_code'=> 'required|string|max:50',
            'address.state'      => 'nullable|string|max:255',
            'address.country'    => 'required|string|size:2',
            'mode'   => 'required|in:trial,immediate',
        ]);

        $customer = \Stripe\Customer::create([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'address' => [
                'line1'       => $validated['address']['line1'],
                'city'        => $validated['address']['city'],
                'postal_code' => $validated['address']['postal_code'],
                'state'       => $validated['address']['state'] ?? null,
                'country'     => strtoupper($validated['address']['country']),
            ],
        ]);

        $lineItem = [
            'price_data' => [
                'currency'     => 'inr',
                'recurring'    => ['interval' => 'month'],
                'unit_amount'  => (int) round($validated['amount'] * 100),
                'product_data' => [
                    'name'        => 'Subscription (' . $validated['licenses'] . ' licenses)',
                    'description' => 'Monthly subscription (custom price)',
                ],
            ],
            'quantity' => $validated['licenses'],
        ];

        $subscriptionData = [];
        if ($validated['mode'] === 'trial') {
            $subscriptionData['trial_period_days'] = 7;
        }

        $session = CheckoutSession::create([
            'mode'                   => 'subscription',
            'payment_method_types'   => ['card'],
            'customer'               => $customer->id,
            'line_items'             => [$lineItem],
            'subscription_data'      => $subscriptionData,
            'allow_promotion_codes'  => true,
            'success_url' => route('subscribe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('subscribe.cancel'),
        ]);

        return redirect($session->url);
    }

    // Success page
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) abort(404);

        $session = CheckoutSession::retrieve($sessionId);
        \Log::info('Checkout Completed', ['session' => $session]);

        return view('shared.subscription.success', ['session' => $session]);
    }

    // Cancel page
    public function cancel()
    {
        return view('shared.subscription.cancel');
    }

    // Manage page (demo)
    public function manage()
    {
        $currentPlanAmount = 7000;
        $subscriptionId    = 'sub_1RzIV7SE73ipZPK0kBdYHEYI'; // replace later
        return view('shared.subscription.manage', compact('currentPlanAmount', 'subscriptionId'));
    }

    /**
     * Update subscription
     * - switch: change price (plan change)
     * - increase_license: add seats (immediate prorated charge)
     */
    public function change(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => 'required|string',
            'action'          => 'required|in:switch,increase_license',
            'new_amount'      => 'nullable|numeric|min:0.5',
            'additional_license'  => 'nullable|integer|min:1',
        ]);

        $subscription = \Stripe\Subscription::retrieve($validated['subscription_id']);
        $subscriptionItem = $subscription->items->data[0];

        if ($validated['action'] === 'switch') {
            // 🔹 Switch plan (price change)
            $updated = \Stripe\Subscription::update(
                $validated['subscription_id'],
                [
                    'items' => [[
                        'id' => $subscriptionItem->id,
                        'price_data' => [
                            'currency'    => 'inr',
                            'recurring'   => ['interval' => 'month'],
                            'unit_amount' => (int) round($validated['new_amount'] * 100),
                            'product_data' => [
                                'name'        => 'Switched Plan',
                                'description' => 'User changed to new plan',
                            ],
                        ],
                        'quantity' => $subscriptionItem->quantity,
                    ]],
                    'proration_behavior' => 'create_prorations',
                ]
            );

        } elseif ($validated['action'] === 'increase_license') {
            // 🔹 Increase licenses
            $newQty = $subscriptionItem->quantity + $validated['additional_license'];

            $updated = \Stripe\Subscription::update(
                $validated['subscription_id'],
                [
                    'items' => [[
                        'id'       => $subscriptionItem->id,
                        'quantity' => $newQty,
                    ]],
                    'proration_behavior' => 'create_prorations',
                ]
            );
        }

        return back()->with('status', 'Subscription updated!');
    }

    /**
     * Cancel subscription (immediate or at period end)
     */
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
}
