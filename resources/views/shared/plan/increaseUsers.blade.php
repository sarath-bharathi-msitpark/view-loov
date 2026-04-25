{{ Form::open(['url' => route('stripe.license.change'), 'enctype' => "multipart/form-data", 'class'=>'needs-validation', 'novalidate']) }}
<div class="modal-body">

    <div class="row mb-4 align-items-center">
        <label for="additional_license" class="col-md-4 col-form-label text-md-start fw-bold">Enter Licenses</label>
        <div class="col-md-8">
            <input type="number" min="1" class="form-control" value="1" name="additional_license"
                   id="additional_license" required>
        </div>
    </div>

    <input type="hidden" name="plan_id" value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
    <input type="hidden" id="basePrice" value="{{ $plan->price }}">
    <input type="hidden" id="taxRate" value="{{ $plan->tax ?? 0 }}">

    <div class="row">
        <div class="col-md-12">
            <div class="border rounded p-4 bg-light">

                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">Base Price per License:</span>
                    <span>₹{{ number_format($plan->price, 2) }}</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">Subtotal:</span>
                    <span>₹<span id="subTotalAmount">0.00</span></span>
                </div>
                <!--<div class="text-end text-muted mb-2" id="userCountDisplay" style="font-size: 0.9rem;"></div>-->

                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">GST ({{ $plan->tax ?? 0 }}%):</span>
                    <span>₹<span id="gstAmount">0.00</span></span>
                </div>

                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                    <strong>Total Payable:</strong>
                    <strong>₹<span class="totalAmount">0.00</span></strong>
                </div>

                @php
                    $expiryDate = \Carbon\Carbon::parse($plan_expiry_date)->format('d M Y');
                    $existingAmount = $plan->getTotalPlanAmount($plan->id);
                @endphp

                    <!--<div class="alert alert-info mt-4">-->
                <!--    <p class="mb-0">-->
                <!--        You are adding <strong id="userCountDisplay"></strong> license(s) to your subscription. -->
                <!--        An immediate charge of <strong>₹<span id="additionalAmount">0.00</span></strong> will apply for the new licenses. -->
                <!--        From your next billing cycle, your subscription will automatically renew at the updated total amount. -->
                <!--        <br>-->
                <!--        <strong>Note:</strong> Your current plan expires on <strong>{{ $expiryDate }}</strong>. After that, you will be charged your existing plan amount plus the additional licenses purchased now. -->
                <!--        The total payable after <strong>{{ $expiryDate }}</strong> will be <strong>₹<span id="grandTotalAmount" class="fs-5">0.00</span></strong>.-->
                <!--    </p>-->
                <!--</div>-->
                <div class="alert alert-info mt-4">
                    <p class="mb-2"><strong>Your Plan Update:</strong></p>
                    <div class="ps-1">
                        <p class="mb-1">
                            📅 <strong>Current Plan Ends:</strong> <span class="text-primary">{{ $expiryDate }}</span>
                        </p>
                        <p class="mb-1">
                            💰 <strong>Pay Now:</strong> <span class="text-success fw-bold">₹<span class="totalAmount">0.00</span></span>
                            (for extra licenses)
                        </p>
                        <p class="mb-0">
                            🔄 <strong>From Next Billing After {{ $expiryDate }}:</strong>
                            <span class="text-danger fw-bold fs-5">₹<span id="grandTotalAmount">0.00</span></span>
                            / {{ $plan->duration }}
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary">{{ __('Confirm & Proceed') }}</button>
</div>
{{ Form::close() }}


<script>
    function calculateUserCost() {
        let userCount = parseInt($('#additional_license').val());
        let basePrice = parseFloat($('#basePrice').val()) || 0;
        let taxRate = parseFloat($('#taxRate').val()) || 0;

        if (isNaN(userCount) || userCount <= 0) {
            $('#subTotalAmount').text('0.00');
            $('#gstAmount').text('0.00');
            $('#totalAmount').text('0.00');
            $('#userCountDisplay').text('');
            return;
        }

        let subTotal = Math.round(userCount * basePrice);
        let gstAmount = Math.round((subTotal * taxRate) / 100);
        let total = Math.round(subTotal + gstAmount);

        $('#subTotalAmount').text(subTotal.toFixed(2));
        $('#gstAmount').text(gstAmount.toFixed(2));
        $('.totalAmount').text(total.toFixed(2));
        $('#userCountDisplay').text(`(${userCount} user${userCount > 1 ? 's' : ''} × ₹${basePrice.toFixed(2)})`);
        let existingPlanAmount = {{ $existingAmount }};
        let grandTotal = existingPlanAmount + total;
        $('#grandTotalAmount').text(grandTotal.toFixed(2));
    }

    $(document).ready(function () {
        $('#additional_license').on('input change keyup', calculateUserCost);
        calculateUserCost();
    });
</script>
