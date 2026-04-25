<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Manage Subscription</title></head>
<body>
<h1>Manage Subscription</h1>

@if (session('status'))
    <p style="color:green;">{{ session('status') }}</p>
@endif

<p>Current amount (USD): ${{ number_format($currentPlanAmount, 2) }}</p>

<h3>Change Plan Amount</h3>
<form action="{{ route('subscription.change') }}" method="POST">
    @csrf
    <label>Stripe Subscription ID</label>
    <input type="text" name="subscription_id" value="{{ $subscriptionId }}" required style="width:100%;"><br><br>

    <label>New Amount (USD)</label>
    <input type="number" name="new_amount" step="0.01" min="0.5" value="70.00" required style="width:100%;"><br><br>
    
    
        <label>Additional Licenses</label>
        <input type="number" name="additional_license" step="1" min="1" value="1" required style="width:100%;"><br><br>
        
        
        <h3>Action</h3>
        <label><input type="radio" name="action" value="increase_license" checked> Buy License</label><br>
        <label><input type="radio" name="action" value="switch"> Buy Now</label><br><br>

    <button type="submit">Update Subscription</button>
</form>

<h3>Cancel Subscription</h3>
<form action="{{ route('subscription.cancel') }}" method="POST">
    @csrf
    <label>Stripe Subscription ID</label>
    <input type="text" name="subscription_id" value="{{ $subscriptionId }}" required style="width:100%;"><br><br>

    <label><input type="checkbox" name="at_period_end" value="1"> Cancel at period end (instead of immediate)</label><br><br>

    <button type="submit">Cancel</button>
</form>

<p style="margin-top:30px;"><a href="{{ route('subscribe.show') }}">← Back to Subscribe</a></p>
</body>
</html>
