<?php

use App\Http\Controllers\Payment\BankTransferPaymentController;
use App\Http\Controllers\Web\Shared\CouponController;
use App\Http\Controllers\Payment\CashfreeController;
use App\Http\Controllers\Payment\StripeSubscriptionController;

//Apply Coupon
Route::get('/apply-coupon', [CouponController::class, 'applyCoupon'])->name('apply.coupon')->middleware(['auth', 'XSS', 'revalidate']);

//plan-order
Route::post('order/{id}/changeaction', [BankTransferPaymentController::class, 'changeStatus'])->name('order.changestatus');
Route::delete('order/{id}', [BankTransferPaymentController::class, 'orderDestroy'])->name('order.destroy');
Route::get('order/{id}/action', [BankTransferPaymentController::class, 'action'])->name('order.action');

Route::post('/customer-pay-with-bank', [BankTransferPaymentController::class, 'customerPayWithBank'])->name('customer.pay.with.bank')->middleware(['XSS']);
Route::get('invoice/{id}/action', [BankTransferPaymentController::class, 'invoiceAction'])->name('invoice.action');
Route::post('invoice/{id}/changeaction', [BankTransferPaymentController::class, 'invoiceChangeStatus'])->name('invoice.changestatus');
Route::post('plan-pay-with-bank', [BankTransferPaymentController::class, 'planPayWithBank'])->name('plan.pay.with.bank')->middleware(['auth', 'XSS', 'revalidate']);

Route::post('cashfree/payments/store', [CashfreeController::class, 'startSubscription'])->name('plan.pay.with.cashfree');
Route::get('cashfree/payments/pay', [CashfreeController::class, 'cashfreePaymentPayPage'])->name('plan.pay.cashfree');
Route::any('cashfree/payments/subscription/success', [CashfreeController::class, 'cashfreePaymentSuccess'])->name('cashfreePayment.success');
Route::any('cashfree/upgradelicense/success', [CashfreeController::class, 'upgradeLicensesSuccess'])->name('cashfreePayment.upgradelicense.success');
Route::post('cashfree/webhook', [CashfreeController::class, 'handleWebhook'])->name('cashfreePayment.webhook');

Route::post('/stripe/subscription', [StripeSubscriptionController::class, 'createCheckoutSession'])->name('stripe.checkout')->middleware(['auth', 'XSS', 'revalidate']);
Route::get('/stripe/subscription/success', [StripeSubscriptionController::class, 'success'])->name('stripe.success')->middleware(['auth', 'XSS', 'revalidate']);
Route::get('stripe/subscription/cancel', [StripeSubscriptionController::class, 'cancelSubscription'])->name('stripe.cancel')->middleware(['auth', 'XSS', 'revalidate']);
Route::post('/subscription/license', [StripeSubscriptionController::class, 'addLicense'])->name('stripe.license.change')->middleware(['auth', 'XSS', 'revalidate']);
Route::post('/stripe/webhook', [StripeSubscriptionController::class, 'webhookHandle'])->name('stripe.webhook');