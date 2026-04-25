<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Redirecting to Subscription Checkout…</title>
  <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
</head>
<body>
    <p>Redirecting to payment…</p>
    @php
        $payment_setting = \App\Models\Utility::getAdminPaymentSetting();
        $mode = $payment_setting['cashfree_mode'] ?? 'sandbox';
    @endphp
  <script>
      const cashfreeMode = "{{ $mode === 'production' ? 'production' : 'sandbox' }}"; 
    const cashfree = Cashfree({ mode: cashfreeMode });
    cashfree.subscriptionsCheckout({
      subsSessionId: "{{ $sessionId }}",
      redirectTarget: "_self"  // opens checkout in the same tab
    }).catch(error => {
      document.body.innerHTML = "<p style='color:red;'>Error initializing checkout: " + (error.message || error) + "</p>";
    });
  </script>
</body>
</html>
