<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TabPaymentController extends Controller
{
    // Show payment form
    public function showForm()
    {
        $payment = [];
        // Get payment URL for iFrame
        $paymentUrl = $this->initiateTapPayment($payment);

        // Return view with iFrame
        return view('frontend.pay_gateway.tap.create', [
            'iframe_url' => $paymentUrl,
            'payment' => $payment
        ]);

//        return view('frontend.pay_gateway.tap.create', []);
    }

    // Process payment form submission
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'number_of_month' => 'required|numeric|min:1',
            'bill_adjustments' => 'numeric|min:0',
        ]);

        // Calculate amount
        $monthlyBill = 200;
        $amount = 100;

        // Create payment record
         $payment = [
            'user_id' => auth()->id(),
            'amount' => $amount,
            'status' => 'pending',
            'reference_id' => 'TAP-' . Str::random(16),
            'months' => $validated['number_of_month'],
            'adjustments' => $request->bill_adjustments ?? 0,
        ];

        // Initiate TAP payment
        return $this->initiateTapPayment($payment);
    }

    // Get TAP authentication token
    protected function getTapAuthToken()
    {
        try {
            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => 'Basic ' . config('tap_gateway.tap.auth_token'),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->post(config('tap_gateway.tap.auth_url'), [
                    'grant_type' => 'password',
                    'username' => config('tap_gateway.tap.username'),
                    'password' => config('tap_gateway.tap.password'),
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Authentication failed: ' . $response->body());
        } catch (\Exception $e) {
            throw new \Exception('Failed to get TAP token: ' . $e->getMessage());
        }
    }

    // Initiate TAP payment
    protected function initiateTapPayment($payment)
    {
        try {
            // Get auth token
            $authResponse = $this->getTapAuthToken();

            // Prepare payment URL with parameters
//            return config('tap_gateway.tap.api_key');
            $paymentUrl = config('tap_gateway.tap.payment_url') . '?' . http_build_query([
                    'token' => $authResponse['access_token'],
                    'authAPIKey' => "18d9a36b-bb81-4f46-a79f-47c62251e6armoc",
                    'paymentMode' => 'iFrame',
                    'requestorReferenceId' => 11,
                    'callBackUrl' => "https://trustaxiatapay.com",
                    'amount' => 200,
                    'currency' => 'BDT',
                    'invoiceNumber' => 11,
                    'additionalInformation' => 'Membership payment for ' . 2 . ' months',
                ]);
            return $paymentUrl;
            // Redirect to TAP payment page
            return redirect()->away($paymentUrl);

        } catch (\Exception $e) {
            return back()->with('error', 'Payment initiation failed: ' . $e->getMessage());
        }
    }

    // Handle payment callback
    public function handleCallback(Request $request)
    {
        $referenceId = $request->input('requestorReferenceId');
        $status = $request->input('status');
        $transactionId = $request->input('transactionId');

//        // Find and update payment
//        $payment = Payment::where('reference_id', $referenceId)->firstOrFail();
//        $payment->update([
//            'status' => $status,
//            'transaction_id' => $transactionId,
//        ]);
        $payment = [];
        // Verify payment status with TAP
        $verified = $this->verifyPayment($payment);

        if ($verified && $status === 'completed') {
            return redirect()->route('payment.success')->with([
                'success' => 'Payment completed successfully',
                'transaction_id' => $transactionId,
                'amount' => 200,
            ]);
        }

        return redirect()->route('frontend.pay_gateway.tap.failed')->with('error', 'Payment failed or was not verified');
    }

    // Verify payment with TAP
    protected function verifyPayment($payment)
    {
        try {
            $authResponse = $this->getTapAuthToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $authResponse['access_token'],
            ])->get(config('tap_gateway.tap.status_url'), [
                'requestorReferenceId' => 11,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['status'] === 'completed';
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Payment success page
    public function success()
    {
        return view('frontend.pay_gateway.tap.success');
    }

    // Payment failed page
    public function failed()
    {
        return view('frontend.pay_gateway.tap.failed');
    }
}
