<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Subscribe</title>
</head>
<body>
    <h1>Stripe Subscription (Trial OR Immediate)</h1>

    @if ($errors->any())
        <div style="color:red;">
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('subscribe.create') }}" method="POST" style="max-width:460px;">
        @csrf

        <h3>Customer</h3>
        <label>Name</label>
        <input type="text" name="name" value="Test User" required style="width:100%;"><br><br>

        <label>Email</label>
        <input type="email" name="email" value="testuser@example.com" required style="width:100%;"><br><br>

        <h3>Billing Address (Non-India for exports)</h3>
        <label>Line 1</label>
        <input type="text" name="address[line1]" value="123 Export Street" required style="width:100%;"><br><br>

        <label>City</label>
        <input type="text" name="address[city]" value="New York" required style="width:100%;"><br><br>

        <label>Postal Code</label>
        <input type="text" name="address[postal_code]" value="10001" required style="width:100%;"><br><br>

        <label>State</label>
        <input type="text" name="address[state]" value="NY" style="width:100%;"><br><br>

        <label>Country (2-letter)</label>
        <input type="text" name="address[country]" value="US" required style="width:100%;"><br><br>

        <h3>Plan</h3>
        <label>Amount</label>
        <input type="number" name="amount" step="0.01" min="0.5" value="70" required style="width:100%;"><br><br>
        
        <h3>Plan</h3>
        <label>Licenses</label>
        <input type="number" name="licenses" step="1" min="1" value="1" required style="width:100%;"><br><br>

        <h3>Start Type</h3>
        <label><input type="radio" name="mode" value="trial" checked> 7-day Free Trial</label><br>
        <label><input type="radio" name="mode" value="immediate"> Buy Now (Charge Immediately)</label><br><br>

        <button type="submit">Continue to Stripe Checkout</button>
    </form>

    <hr>
    <p>After success, you’ll be redirected to manage your subscription.</p>
</body>
</html>
