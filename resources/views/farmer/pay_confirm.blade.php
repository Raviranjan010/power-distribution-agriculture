@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-bold text-theme-heading mb-1">Confirm Payment</h2>
    <p class="text-sm text-theme-text">Review your bill details before proceeding</p>
</div>

<div class="max-w-2xl mx-auto utilitarian-card p-6">
    <div class="mb-6 pb-6 border-b border-theme-border/50">
        <h3 class="text-lg font-bold text-theme-heading mb-4">Bill Details</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Bill Number</p>
                <p class="font-bold text-theme-heading">{{ $bill->bill_number }}</p>
            </div>
            <div>
                <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Connection</p>
                <p class="font-bold text-theme-heading">{{ $conn->connection_number }}</p>
            </div>
            <div>
                <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Billing Period</p>
                <p class="font-bold text-theme-heading">{{ \Carbon\Carbon::create($bill->billing_year, $bill->billing_month)->format('M Y') }}</p>
            </div>
            <div>
                <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Units Consumed</p>
                <p class="font-bold text-theme-heading">{{ number_format($bill->units_consumed) }} kWh</p>
            </div>
            <div class="col-span-2 mt-2">
                <p class="text-[10px] text-theme-text font-bold tracking-widest uppercase mb-1">Net Payable Amount</p>
                <p class="text-3xl font-bold text-amber-400">₹{{ number_format($bill->net_payable, 2) }}</p>
            </div>
        </div>
    </div>

    @if($razorpayOrderId)
        <button id="rzp-button1" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-3 rounded-lg transition-colors flex justify-center items-center gap-2">
            <i class="fa-solid fa-lock"></i> Pay Securely with Razorpay
        </button>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            var options = {
                "key": "{{ env('RAZORPAY_KEY') }}",
                "amount": "{{ $bill->net_payable * 100 }}",
                "currency": "INR",
                "name": "Power Distribution",
                "description": "Bill Payment {{ $bill->bill_number }}",
                "order_id": "{{ $razorpayOrderId }}",
                "handler": function (response) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('farmer.bill.pay', $bill->id) }}";
                    
                    var csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = "{{ csrf_token() }}";
                    form.appendChild(csrf);

                    var orderId = document.createElement('input');
                    orderId.type = 'hidden';
                    orderId.name = 'razorpay_order_id';
                    orderId.value = response.razorpay_order_id;
                    form.appendChild(orderId);

                    var paymentId = document.createElement('input');
                    paymentId.type = 'hidden';
                    paymentId.name = 'razorpay_payment_id';
                    paymentId.value = response.razorpay_payment_id;
                    form.appendChild(paymentId);

                    var signature = document.createElement('input');
                    signature.type = 'hidden';
                    signature.name = 'razorpay_signature';
                    signature.value = response.razorpay_signature;
                    form.appendChild(signature);

                    document.body.appendChild(form);
                    form.submit();
                },
                "prefill": {
                    "name": "{{ Auth::user()->name }}",
                    "email": "{{ Auth::user()->email }}",
                    "contact": "{{ Auth::user()->phone }}"
                },
                "theme": {
                    "color": "#10b981"
                }
            };
            var rzp1 = new Razorpay(options);
            document.getElementById('rzp-button1').onclick = function(e){
                rzp1.open();
                e.preventDefault();
            }
        </script>
    @else
        <form method="POST" action="{{ route('farmer.bill.pay', $bill->id) }}">
            @csrf
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-4 py-3 rounded-lg transition-colors flex justify-center items-center gap-2">
                <i class="fa-solid fa-check"></i> Confirm Payment
            </button>
            <p class="text-[10px] text-theme-text text-center mt-3">This will simulate payment processing.</p>
        </form>
    @endif
</div>
@endsection
