<?php

namespace App\Http\Controllers\Payment;
use App\Http\Controllers\Controller;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Utility;
use App\Models\UserCoupon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PhpParser\Node\Stmt\TryCatch;
use Illuminate\Support\Facades\Http;

class CashfreeController extends Controller
{
    public function paymentConfig()
    {
        if(\Auth::check()){
            $payment_setting = Utility::getAdminPaymentSetting();
            config(
                [
                    'services.cashfree.key' => isset($payment_setting['cashfree_api_key']) ? $payment_setting['cashfree_api_key'] : '',
                    'services.cashfree.secret' => isset($payment_setting['cashfree_secret_key']) ? $payment_setting['cashfree_secret_key'] : '',
                    'services.cashfree.currency' =>!empty($payment_setting['currency']) ? $payment_setting['currency'] : 'USD'
                ]
            );
        }
    }
    public function cashfreePaymentStore(Request $request)
    {
        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        $user = \Auth::user();
        $this->paymentConfig();
        $url = config('services.cashfree.url');

        if ($plan) {
            
            $get_amount = ($plan->getTotalPlanAmount($plan->id));
            
            $additional_license = 0;
            if($request->has('additional_license') && $request->additional_license > 0) {
                $additional_license = $request->additional_license;
                $subTotal = $additional_license * $plan->price;
                $gstAmount = ($subTotal * $plan->tax) / 100;
                $get_amount = $subTotal + $gstAmount;
                
            }
            try {
                if (!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun = $coupons->used_coupon();
                        
                        $discount_value = ($get_amount / 100) * $coupons->discount;
                        $price          = $get_amount - $discount_value;

                        if ($coupons->limit == $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                        if ($get_amount <= 0) {
                            $authuser = \Auth::user();
                            $authuser->plan = $plan->id;
                            $authuser->save();
                            $assignPlan = $authuser->assignPlan($plan->id);
                            if ($assignPlan['is_success'] == true && !empty($plan)) {
                                if (!empty($authuser->cashfree_subscription_id) && $authuser->cashfree_subscription_id != '') {
                                    try {
                                        $authuser->cancel_subscription($authuser->id);
                                    } catch (\Exception $exception) {
                                        \Log::debug($exception->getMessage());
                                    }
                                }
                                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                                $userCoupon = new UserCoupon();
                                $userCoupon->user = $authuser->id;
                                $userCoupon->coupon = $coupons->id;
                                $userCoupon->order = $orderID;
                                $userCoupon->save();
                                Order::create(
                                    [
                                        'order_id' => $orderID,
                                        'name' => null,
                                        'email' => null,
                                        'card_number' => null,
                                        'card_exp_month' => null,
                                        'card_exp_year' => null,
                                        'plan_name' => $plan->name,
                                        'plan_id' => $plan->id,
                                        'price' => $get_amount == null ? 0 : $get_amount,
                                        'price_currency' => config('services.cashfree.currency'),
                                        'txn_id' => '',
                                        'payment_type' => 'Cashfree',
                                        'payment_status' => 'success',
                                        'receipt' => null,
                                        'user_id' => $authuser->id,
                                    ]
                                );
                                $assignPlan = $authuser->assignPlan($plan->id);
                                return redirect()->route('general.plans.index')->with('success', __('Plan Successfully Activated'));
                            }
                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }
                $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                
                $autoRenew = true;
                if($additional_license == 0 && $autoRenew) {
                    return $this->createCashfreeSubscription($request, $plan->id);
                }

                $headers = array(
                    "Content-Type: application/json",
                    "x-api-version: 2022-01-01",
                    "x-client-id: " . config('services.cashfree.key'),
                    "x-client-secret: " . config('services.cashfree.secret')
                );

                $data = json_encode([
                    'order_id' => $orderID,
                    'order_amount' => $get_amount,
                    "order_currency" => config('services.cashfree.currency'),
                    "order_name" => $plan->name,
                    "customer_details" => [
                        "customer_id" => 'customer_' . $user->id,
                        "customer_name" => $user->name,
                        "customer_email" => $user->email,
                        "customer_phone" => $user->mobile_no,
                    ],
                    "order_meta" => [
                        "return_url" => route('cashfreePayment.success') . '?order_id={order_id}&order_token={order_token}&plan_id=' . $plan->id . '&amount=' . $get_amount . '&coupon=' . $coupon . '&additional_license=' . $additional_license . ''

                    ]
                ]);
                try {

                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                    $resp = curl_exec($curl);
                    curl_close($curl);
                    
                    return redirect()->to(json_decode($resp)->payment_link);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Currency Not Supported.Contact To Your Site Admin');
                }



            } catch (\Exception $e) {

                return redirect()->back()->with('error', $e);
            }

        } else {
            return redirect()->route('general.plans.index')->with('error', __('Plan is deleted.'));
        }


    }

    public function cashfreePaymentSuccess(Request $request)
    {
        $this->paymentConfig();
        $user = \Auth::user();
        $plan = Plan::find($request->plan_id);
        $couponCode = $request->coupon;
        $getAmount = $request->amount;
        $additionalLicense = $request->additional_license;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', config('services.cashfree.url') . '/' . $request->get('order_id') . '/settlements', [
                'headers' => [
                    'accept' => 'application/json',
                    'x-api-version' => '2022-09-01',
                    "x-client-id" => config('services.cashfree.key'),
                    "x-client-secret" => config('services.cashfree.secret')
                ],
            ]);


            $respons = json_decode($response->getBody());
            if ($respons->order_id && $respons->cf_payment_id != NULL) {

                $response = $client->request('GET', config('services.cashfree.url') . '/' . $respons->order_id . '/payments/' . $respons->cf_payment_id . '', [
                    'headers' => [
                        'accept' => 'application/json',
                        'x-api-version' => '2022-09-01',
                        'x-client-id' => config('services.cashfree.key'),
                        'x-client-secret' => config('services.cashfree.secret'),
                    ],
                ]);
                $info = json_decode($response->getBody());


                if ($info->payment_status == "SUCCESS") {
                    Utility::referralTransaction($plan);

                    $order = new Order();
                    $order->order_id = $orderID;
                    $order->name = $user->name;
                    $order->card_number = '';
                    $order->card_exp_month = '';
                    $order->card_exp_year = '';
                    $order->plan_name = $plan->name;
                    $order->plan_id = $plan->id;
                    $order->price = $getAmount;
                    $order->price_currency = !empty($payment_setting['currency']) ? $payment_setting['currency'] : 'USD';
                    $order->payment_type = __('Cashfree');
                    $order->payment_status = 'success';
                    $order->txn_id = '';
                    $order->receipt = '';
                    $order->user_id = $user->id;
                    $order->save();
                    
                    
                    $coupons = Coupon::find($request->coupon_id);
                    if (!empty($request->coupon_id)) {
                        if (!empty($coupons)) {
                            $userCoupon = new UserCoupon();
                            $userCoupon->user = $user->id;
                            $userCoupon->coupon = $coupons->id;
                            $userCoupon->order = $orderID;
                            $userCoupon->save();
                            $usedCoupun = $coupons->used_coupon();
                            if ($coupons->limit <= $usedCoupun) {
                                $coupons->is_active = 0;
                                $coupons->save();
                            }
                        }
                    }
                    
                    if($additionalLicense > 0) {
                        
                        $additional_license = $request->additional_license;
                        $subTotal = $additional_license * $plan->price;
                        $gstAmount = ($subTotal * $plan->tax) / 100;
                        
                        $plan->max_users += $additionalLicense;
                        $plan->total_amount = $subTotal + $gstAmount;
                        $plan->save();
                        
                        $this->updateCashfreePlanRecurringAmount($user->cashfree_subscription_id, $plan->total_amount);
                        return redirect()->route('general.plans.index')->with('success', __('License activated Successfully.'));
                    }else {
                        $assignPlan = $user->assignPlan($plan->id);
                    }

                    if ($assignPlan['is_success']) {
                        return redirect()->route('general.plans.index')->with('success', __('Plan activated Successfully.'));
                    } else {
                        return redirect()->route('general.plans.index')->with('error', __($assignPlan['error']));
                    }

                } else {
                    return redirect()->route('general.plans.index')->with('error', __('Your Transaction is fail please try again'));
                }
            } else {
                return redirect()->route('general.plans.index')->with('error', 'Payment Failed.');
            }
            return redirect()->route('general.plans.index')->with('success', 'Plan activated Successfully.');
        } catch (\Exception $e) {
            return redirect()->route('general.plans.index')->with('error', __($e->getMessage()));
        }

    }

    public function invoicepaywithcashfree(Request $request)
    {
        $invoice_id = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice = Invoice::find($invoice_id);

        $this->invoiceData = $invoice;

        try {
            $user      = User::find($invoice->created_by);
            $settings= Utility::settingsById($invoice->created_by);
            $companyPaymentSettings = Utility::getCompanyPaymentSetting($user->id);

            config(
                [
                    'services.cashfree.key' => isset($companyPaymentSettings['cashfree_api_key']) ? $companyPaymentSettings['cashfree_api_key'] : '',
                    'services.cashfree.secret' => isset($companyPaymentSettings['cashfree_secret_key']) ? $companyPaymentSettings['cashfree_secret_key'] : '',
                ]
            );
            $url = config('services.cashfree.url');
            if (\Auth::check()) {
                $user = Auth::user();
            } else {
                $user = User::where('id', $invoice->created_by)->first();
            }
            $get_amount = $request->amount;
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            if ($invoice && $get_amount != 0) {
                if ($get_amount > $invoice->getDue())
                {
                    return redirect()->back()->with('error', __('Invalid amount.'));
                }
                $headers = array(
                    "Content-Type: application/json",
                    "x-api-version: 2022-01-01",
                    "x-client-id: " . config('services.cashfree.key'),
                    "x-client-secret: " . config('services.cashfree.secret')
                );

                $data = json_encode([
                    'order_id' => $orderID,
                    'order_amount' => $get_amount,
                    "order_currency" => 'INR',
                    "order_name" => $invoice->name,
                    "customer_details" => [
                        "customer_id" => 'customer_' . $user->id,
                        "customer_name" => $user->name,
                        "customer_email" => $user->email,
                        "customer_phone" => '1234567890',
                    ],
                    "order_meta" => [
                        "return_url" => route('invoice.cashfreePayment.success') . '?order_id={order_id}&invoice_id=' . $invoice_id . '&amount=' . $get_amount
                    ]
                ]);

                try {

                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                    $resp = curl_exec($curl);
                    curl_close($curl);
                    return redirect()->to(json_decode($resp)->payment_link);
                } catch (\Throwable $th) {

                    return redirect()->back()->with('error', 'Currency Not Supported.Contact To Your Site Admin');
                }
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }

    public function getInvoicePaymentStatus(Request $request)
    {

        $invoice    = Invoice::find($request->invoice_id);
        $orderID  = strtoupper(str_replace('.', '', uniqid('', true)));
        $user      = User::find($invoice->created_by);
        $settings= Utility::settingsById($invoice->created_by);
        $companyPaymentSettings = Utility::getCompanyPaymentSetting($user->id);

        config(
            [
                'services.cashfree.key' => isset($companyPaymentSettings['cashfree_api_key']) ? $companyPaymentSettings['cashfree_api_key'] : '',
                'services.cashfree.secret' => isset($companyPaymentSettings['cashfree_secret_key']) ? $companyPaymentSettings['cashfree_secret_key'] : '',
            ]
        );

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', config('services.cashfree.url') . '/' . $request->get('order_id') . '/settlements', [
            'headers' => [
                'accept' => 'application/json',
                'x-api-version' => '2022-09-01',
                "x-client-id" => config('services.cashfree.key'),
                "x-client-secret" => config('services.cashfree.secret')
            ],
        ]);
        $respons = json_decode($response->getBody());
        if ($respons->order_id && $respons->cf_payment_id != NULL) {

            $response = $client->request('GET', config('services.cashfree.url') . '/' . $respons->order_id . '/payments/' . $respons->cf_payment_id . '', [
                'headers' => [
                    'accept' => 'application/json',
                    'x-api-version' => '2022-09-01',
                    'x-client-id' => config('services.cashfree.key'),
                    'x-client-secret' => config('services.cashfree.secret'),
                ],
            ]);
            $info = json_decode($response->getBody());
            try {

                if ($info->payment_status == "SUCCESS") {

                    $invoice_payment                 = new InvoicePayment();
                    $invoice_payment->invoice_id     = $request->invoice_id;
                    $invoice_payment->date           = Date('Y-m-d');
                    $invoice_payment->amount         = $request->has('amount') ? $request->amount : 0;
                    $invoice_payment->account_id         = 0;
                    $invoice_payment->payment_method         = 0;
                    $invoice_payment->order_id      =$orderID;
                    $invoice_payment->payment_type   = 'Cashfree';
                    $invoice_payment->receipt     = '';
                    $invoice_payment->reference     = '';
                    $invoice_payment->description     = 'Invoice ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id);

                    $invoice_payment->save();

                    if($invoice->getDue() <= 0)
                    {
                        $invoice->status = 4;
                        $invoice->save();
                    }
                    elseif(($invoice->getDue() - $invoice_payment->amount) == 0)
                    {
                        $invoice->status = 4;
                        $invoice->save();
                    }
                    else
                    {
                        $invoice->status = 3;
                        $invoice->save();
                    }



                    //For Notification
                    $setting  = Utility::settingsById($invoice->created_by);
                    $customer = Customer::find($invoice->customer_id);
                    $notificationArr = [
                        'payment_price' => $request->amount,
                        'invoice_payment_type' => 'Cashfree',
                        'customer_name' => $customer->name,
                    ];

                    //Slack Notification
                    if(isset($setting['payment_notification']) && $setting['payment_notification'] ==1)
                    {
                        Utility::send_slack_msg('new_invoice_payment', $notificationArr,$invoice->created_by);
                    }
                    //Telegram Notification
                    if(isset($setting['telegram_payment_notification']) && $setting['telegram_payment_notification'] == 1)
                    {
                        Utility::send_telegram_msg('new_invoice_payment', $notificationArr,$invoice->created_by);
                    }
                    //Twilio Notification
                    if(isset($setting['twilio_payment_notification']) && $setting['twilio_payment_notification'] ==1)
                    {
                        Utility::send_twilio_msg($customer->contact,'new_invoice_payment', $notificationArr,$invoice->created_by);
                    }

                    //webhook
                    $module ='New Invoice Payment';
                    $webhook=  Utility::webhookSetting($module,$invoice->created_by);
                    if($webhook)
                    {
                        $parameter = json_encode($invoice_payment);
                        $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                        if($status == true)
                        {
                            return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed.'));
                        }
                        else
                        {
                            return redirect()->back()->with('error', __('Payment successfully, Webhook call failed.'));
                        }
                    }



                   //for customer balance update
                    Utility::updateUserBalance('customer', $invoice->customer_id, $request->amount, 'debit');

                    $request->session()->forget('invoice_data');

                    return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('success', __('Invoice paid Successfully!'));

                } else {
                    return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', __('Transaction fail'));

                }
            } catch (\Exception $e) {

                return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', $e->getMessage());

            }
        } else {
            return redirect()->route('invoice.link.copy', \Crypt::encrypt($invoice->id))->with('error', 'Payment Failed.');
        }
    }
    
    public function cashfreePaymentPayPage() {
        return view('shared.subscription.cashfree');
    }
    
    public function createCashfreeSubscription(Request $request, $plan_id)
    {
        $plan = Plan::findOrFail($plan_id);
        $user = \Auth::user();
        
        if ($user->cashfree_subscription_id) {
            $this->cancelCashfreeSubscription($user->cashfree_subscription_id);
        }
        // // $plan->cashfree_plan_id = null;
        // // $plan->save();
        // if (!$plan->cashfree_plan_id) {
            ($this->createCashfreePlan($plan));
            $plan->refresh();
        // }
        
        
    
        $baseUrl = config('services.cashfree.subscription_url');
        $subscriptionId = 'sub_' . $user->id . '_' . time();
       
        $returnUrl = route('cashfreePayment.subscription.success') . '?' . http_build_query([
            'plan_id' => $plan->id,
            'coupon' => $request->coupon,
            'additional_license' => $request->additional_license
        ]);
    
        $response = Http::withHeaders([
            "x-client-id"     => config('services.cashfree.key'),
            "x-client-secret" => config('services.cashfree.secret'),
            "Content-Type"    => "application/json",
        ])->post($baseUrl . '/api/v2/subscriptions', [
            "subscriptionId" => $subscriptionId,
            "planId"         => $plan->cashfree_plan_id,
            'customer_id'     => 'cust_' . uniqid(),
            "customerName"   => $user->name,
            "customerEmail"  => $user->email,
            "customerPhone"  => $user->mobile_no,
            "authAmount"     => $plan->total_amount,
            "returnUrl"      => $returnUrl,
            "notifyUrl"      => route('cashfreePayment.webhook'),
            "paymentMethods" => ["card", "upi", "netbanking"]
        ]);
        if ($response->successful() && isset($response->json()['authLink'])) {
            $subscriptionId = $response->json()['subReferenceId'];
            $user->cashfree_subscription_id = $subscriptionId;
            $user->save();
    
            return redirect($response->json()['authLink']);
        }
    // dd($response->json());
        return redirect()->route('general.plans.index')->with('error', 'Unable to create subscription. Try again.');
    }
    
    // public function createCashfreeSubscription(Request $request, $plan_id)
    // {
    //     $plan = Plan::findOrFail($plan_id);
    //     $user = auth()->user();
    
    //     if ($user->cashfree_subscription_id) {
    //         $this->cancelCashfreeSubscription($user->cashfree_subscription_id);
    //     }
    
    //     $this->createCashfreePlan($plan);
    //     $plan->refresh();
    
    //     $baseUrl = 'https://sandbox.cashfree.com/pg';//config('services.cashfree.subscription_url');
    //     $subscriptionId = 'sub_' . $user->id . '_' . time();
    
    //     $returnUrl = route('cashfreePayment.subscription.success', [
    //         'plan_id' => $plan->id,
    //         'coupon' => $request->coupon,
    //         'additional_license' => $request->additional_license,
    //     ]);
    
    //     $response = Http::withHeaders([
    //         'x-client-id'     => config('services.cashfree.key'),
    //         'x-client-secret' => config('services.cashfree.secret'),
    //         'x-api-version'   => '2025-01-01',
    //         'Content-Type'    => 'application/json',
    //     ])->post($baseUrl . '/subscriptions', [
    //         'subscription_id'      => $subscriptionId,
    //         'plan_details'         => [
    //             'plan_id'       => $plan->cashfree_plan_id,
    //         ],
    //         'customer_details'     => [
    //             'customer_id'    => 'cust_' . uniqid(),
    //             'customer_name'  => $user->name,
    //             'customer_email' => $user->email,
    //             'customer_phone' => $user->mobile_no,
    //         ],
    //         'authorization_details' => [
    //             'authorization_amount' => (float) $plan->total_amount,
    //             'payment_methods'      => ['upi', 'cc', 'dc', 'netbanking'],
    //         ],
    //         'subscription_meta'    => [
    //             'return_url'           => $returnUrl,
    //             'notification_channel' => ['EMAIL', 'SMS'],
    //         ],
    //         // optionally: subscription_first_charge_time, expiry, tags
    //     ]);
    // \Log::info($response->json());
    //     if ($response->successful() && $json = $response->json()) {
    //         $sessionId = $json['subscription_session_id'] ?? null;
    //         $subsId   = $json['cf_subscription_id']       ?? null;
    
    //         if ($sessionId && $subsId) {
    //             $user->cashfree_subscription_id = $subsId;
    //             $user->save();
    
    //             return response()->view('shared.subscription.cashfree', [
    //                 'sessionId' => $sessionId,
    //             ]);
    //         }
    //     }
    
    //     return redirect()->route('general.plans.index')
    //         ->with('error', 'Unable to create subscription. Try again.');
    // }


    
    public function cashfreeSubscriptionSuccess(Request $request)
    {
        $user = \Auth::user();
        if(!$user) {
            return redirect()->route('general.plans.index')->with('error', 'Invalid return URL.');
        }
        $subscriptionId = $user->cashfree_subscription_id;
        $planId         = $request->get('plan_id');
        $couponCode     = $request->get('coupon');
        $additionalLicense = $request->get('additional_license');
        
        // try {
        //     $userId = Crypt::decrypt($request->get('uid'));
        //     Auth::loginUsingId($userId);
        // } catch (\Exception $e) {
        //     return redirect()->route('general.plans.index')->with('error', 'Invalid return URL.');
        // }
    
        \Log::info('Cashfree Subscription Success Callback', $request->all());
    
        // $response = Http::withHeaders([
        //     "x-client-id" => config('services.cashfree.key'),
        //     "x-client-secret" => config('services.cashfree.secret'),
        //     "Content-Type" => "application/json",
        // ])->get(config('services.cashfree.subscription_url') . "/api/v2/subscriptions/1223094");
    
        $data = $request->all();

        // if ($response->successful() && ($data = $response->json()) && $data['subscriptionStatus'] == 'ACTIVE') {
        if (isset($data['cf_status']) && $data['cf_status'] === 'ACTIVE') {
            
            
            $plan = Plan::findOrFail($planId);
    
            $orderID = strtoupper(uniqid('CF_', true));
    
            Order::create([
                'order_id' => $orderID,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'price' => $plan->total_amount,
                'price_currency' => 'INR',
                'payment_type' => 'Cashfree Subscription',
                'payment_status' => 'success',
                'txn_id' => $subscriptionId,
            ]);
    
            if ($additionalLicense > 0) {
                $subTotal = $additionalLicense * $plan->price;
                $gstAmount = ($subTotal * $plan->tax) / 100;
                $plan->max_users += $additionalLicense;
                $plan->total_amount = $subTotal + $gstAmount;
                $plan->save();
            }
    
            $user->assignPlan($plan->id);
    
            return redirect()->route('general.plans.index')->with('success', 'Plan activated successfully.');
        } else {
            return redirect()->route('general.plans.index')->with('error', 'Subscription not verified.');
        }
    }

    
    public function cashfreeWebhook(Request $request)
    {
        $payload = $request->all();
        \Log::info('Cashfree Webhook: ' . json_encode($payload));
        return true;
    }
    
    public function createCashfreePlan(Plan $plan)
    {
        try {
            
            $this->paymentConfig();
            
            $baseUrl = config('services.cashfree.subscription_url');
    
            if ((float) $plan->total_amount <= 0) {
                return response()->json(['error' => 'Plan amount must be greater than zero'], 422);
            }
    
            if ($plan->duration == 'month'){
                $period = 1;
                $intervalUnit = "MONTH";
            } elseif ($plan->duration == '3month') {
                $period = 3;
                $intervalUnit = "MONTH";
            } else {
                $period = 1;
                $intervalUnit = "YEAR";
            }
            // $period = 1;
            // $intervalUnit = "DAY";
            
            $payload = [
                "plan_id"               => 'loov_plan_' . $plan->id . '_'.uniqid(),
                "plan_name"             => $plan->name??\Auth::user()->name,
                "plan_type"             => "PERIODIC",
                'plan_currency'         => config('services.cashfree.currency'),
                'plan_status'           =>'ACTIVE',
                "plan_intervals"        => $period,
                "plan_interval_type"    => $intervalUnit, 
                "plan_max_amount"       => 10000,
                "plan_recurring_amount" => (float) $plan->total_amount,
            ];
    
            $response = Http::withHeaders([
                "x-client-id"     => config('services.cashfree.key'),
                "x-client-secret" => config('services.cashfree.secret'),
                "x-api-version" =>  "2025-01-01",
                "Content-Type"    => "application/json",
            ])->post($baseUrl . '/pg/plans', $payload);
    
            if ($response->successful() && isset($response->json()['plan_id'])) {
                $cfPlanId = $response->json()['plan_id'];
    
                $plan->update(['cashfree_plan_id' => $cfPlanId]);
    
                return response()->json([
                    'success'          => true,
                    'message'          => 'Cashfree plan created successfully.',
                    'cashfree_plan_id' => $cfPlanId,
                ]);
            }
            \Log::info(json_encode($response->json()));
            return response()->json([
                'error'   => 'Failed to create Cashfree plan.',
                'details' => $response->json(),
            ], 400);
    
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return response()->json([
                'error' => 'Exception: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function cancelCashfreeSubscription($subscriptionId)
    {
        $baseUrl = config('services.cashfree.subscription_url');
    
        $response = Http::withHeaders([
            'x-client-id'     => config('services.cashfree.key'),
            'x-client-secret' => config('services.cashfree.secret'),
            'Content-Type'    => 'application/json',
        ])->post($baseUrl . '/api/v2/subscriptions/' . $subscriptionId . '/cancel', [
            'cancel_reason' => 'User upgraded plan',
        ]);
        
        \Log::info($response->json());
    
        return $response->successful();
    }
    

    public function updateCashfreePlanRecurringAmount($subscriptionId, $newAmount)
    {
        $this->paymentConfig();
        
        $baseUrl = config('services.cashfree.subscription_url');
    
        $response = Http::withHeaders([
            'x-client-id'     => config('services.cashfree.key'),
            'x-client-secret' => config('services.cashfree.secret'),
            'Content-Type'    => 'application/json',
        ])->put("{$baseUrl}/api/v2/subscriptions/{$subscriptionId}/recurring-amount", [
            'amount' => $newAmount,
        ]);
        // dd($response->json());
        if ($response->successful()) {
            \Log::info("Recurring amount updated successfully");
            return true;
        }
        \Log::error("Failed to update recurring amount", [
            'response' => $response->json()
        ]);
    
        return false;
    }



}
