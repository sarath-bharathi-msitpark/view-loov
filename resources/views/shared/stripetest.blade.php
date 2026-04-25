<!DOCTYPE html>
<html>
<head>
  <title>Stripe Checkout (India export compliant)</title>
  <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
  <h2>Stripe Payment Demo</h2>

  <form id="payment-form">
    @csrf

    <h4>Billing (required for exports)</h4>
    <input id="cust_name" placeholder="Full name" required />
    <input id="cust_email" type="email" placeholder="Email (optional)" />
    <input id="cust_line1" placeholder="Address line 1" required />
    <input id="cust_line2" placeholder="Address line 2 (optional)" />
    <input id="cust_city" placeholder="City" required />
    <input id="cust_state" placeholder="State/Province" required />
    <input id="cust_postal" placeholder="Postal code" required />
    <input id="cust_country" placeholder="Country code (e.g. US)" maxlength="2" required />

    <!-- If you sell physical goods, collect shipping too (recommended) -->
    <details style="margin:8px 0">
      <summary>Shipping address (goods only)</summary>
      <input id="ship_name" placeholder="Recipient name" />
      <input id="ship_line1" placeholder="Ship line 1" />
      <input id="ship_line2" placeholder="Ship line 2" />
      <input id="ship_city" placeholder="Ship city" />
      <input id="ship_state" placeholder="Ship state" />
      <input id="ship_postal" placeholder="Ship postal" />
      <input id="ship_country" placeholder="Ship country code (e.g. US)" maxlength="2" />
    </details>

    <h4>Card</h4>
    <div id="card-element"></div>
    <button id="submit">Pay ₹100</button>
    <div id="payment-message" style="margin-top:10px"></div>
  </form>

  <script>
    const stripe = Stripe("{{ $admin_payment_setting['stripe_key'] }}");
    const elements = stripe.elements();
    const card = elements.create("card");
    card.mount("#card-element");

    document.getElementById("payment-form").addEventListener("submit", async (e) => {
      e.preventDefault();

      // Gather customer + (optional) shipping
      const payload = {
        name:  document.getElementById('cust_name').value.trim(),
        email: document.getElementById('cust_email').value.trim(),
        billing_address: {
          line1:   document.getElementById('cust_line1').value.trim(),
          line2:   document.getElementById('cust_line2').value.trim(),
          city:    document.getElementById('cust_city').value.trim(),
          state:   document.getElementById('cust_state').value.trim(),
          postal_code: document.getElementById('cust_postal').value.trim(),
          country: document.getElementById('cust_country').value.trim().toUpperCase()
        },
        // Include shipping if you're shipping goods
        shipping: {
          name:  document.getElementById('ship_name').value.trim(),
          address: {
            line1: document.getElementById('ship_line1').value.trim(),
            line2: document.getElementById('ship_line2').value.trim(),
            city: document.getElementById('ship_city').value.trim(),
            state: document.getElementById('ship_state').value.trim(),
            postal_code: document.getElementById('ship_postal').value.trim(),
            country: document.getElementById('ship_country').value.trim().toUpperCase()
          }
        }
      };

      // 1) Create PaymentIntent on server with required India-export fields
      const res = await fetch("{{ route('general.teststripe.post') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      if (data.error) {
        document.getElementById("payment-message").innerText = "❌ " + data.error;
        return;
      }

      // 2) Confirm
      const {error, paymentIntent} = await stripe.confirmCardPayment(data.clientSecret, {
        payment_method: { card }
      });

      if (error) {
        document.getElementById("payment-message").innerText = "❌ " + error.message;
      } else if (paymentIntent && paymentIntent.status === "succeeded") {
        document.getElementById("payment-message").innerText = "✅ Payment successful!";
      }
    });
  </script>
</body>
</html>
