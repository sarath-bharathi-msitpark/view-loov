<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Redirecting to Subscription Checkout…</title>
  <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
</head>
<body>
  <p>Redirecting to payment…</p>
  <script>
    const cashfree = Cashfree({ mode: "{{ config('services.cashfree.mode') }}" });
    
    cashfree.checkout({
      paymentSessionId: "{{ $sessionId }}",
      redirectTarget: "_self"  // opens checkout in the same tab
    }).catch(error => {
      document.body.innerHTML = "<p style='color:red;'>Error initializing checkout: " + (error.message || error) + "</p>";
    });
  </script>
</body>
</html>
