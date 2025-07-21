@extends('app1')

@section('title', 'Payment Details')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 pt-4 py-3 px-5">Payment Details</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-semibold mb-2 px-0">Billing Information</h2>
                <p>Amount Due: <span class="font-bold">{{ number_format($payment->amount_due, 2) }} ZMW</span></p>
                <p>Rate: <span class="font-bold">{{ ($payment->rate_per_liter * 100) }} ngwee/liter</span></p>
                <p>Due Date: <span class="font-bold">
                    @if($payment->due_date)
                        {{ $payment->due_date->format('jS M Y') }}
                    @else
                        Not set
                    @endif
                </span></p>
                <p>Status: <span class="font-bold capitalize">{{ $payment->status }}</span></p>
            </div>

            <div>
                <h2 class="text-lg font-semibold mb-2">Usage Summary</h2>
                <p>Rate: <span class="font-bold">{{ ($payment->rate_per_liter ?? 0.20) * 100 }} ngwee per liter</span></p>
                <p>Total Usage: <span class="font-bold">
                    @if($payment->rate_per_liter && $payment->amount_due)
                        {{ number_format($payment->amount_due / $payment->rate_per_liter, 2) }}
                    @else
                        0
                    @endif liters</span></p>
            </div>
        </div>

        <div class="border-t pt-4">
            <h2 class="text-lg font-semibold mb-4">Make Payment, coming soon!!</h2>

            <form action="{{ route('payment.process', $payment) }}" method="POST">
                @csrf

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <button type="button" class="bg-blue-600 text-black py-2 px-4 rounded hover:bg-blue-700">
                        <i class="fas fa-mobile-alt mr-2"></i> Airtel Money
                    </button>
                    <button type="button" class="bg-green-600 text-black py-2 px-4 rounded hover:bg-green-700">
                        <i class="fas fa-mobile mr-2"></i> MTN Money
                    </button>
                    <button type="button" class="bg-purple-600 text-black py-2 px-4 rounded hover:bg-purple-700">
                        <i class="fas fa-university mr-2"></i> Bank Transfer
                    </button>
                    <button type="submit" class="bg-orange-600 text-black py-2 px-4 rounded hover:bg-orange-700">
                        <i class="fas fa-credit-card mr-2"></i> Credit Card
                    </button>
                </div>

                <div class="bg-yellow-50 p-4 rounded-lg">
                    <p class="text-sm"><i class="fas fa-exclamation-circle mr-2"></i>
                        Payments may take up to 24 hours to reflect in our system.
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
