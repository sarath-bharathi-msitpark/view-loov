<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Success</title></head>
<body>
<h1>🎉 Subscription created</h1>
<p>Stripe session: {{ $session->id ?? '' }}</p>
<p>Status: {{ $session->status ?? 'complete' }}</p>

<p><a href="{{ route('subscription.manage') }}">Go to Manage Subscription</a></p>
</body>
</html>
