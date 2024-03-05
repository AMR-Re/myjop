<?php

namespace App\Http\Controllers;

use App\Http\Middleware\donotAllowUserToMakePayment;
use App\Http\Middleware\isEmployer;
use App\Mail\PurchaseMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class SubscriptionController extends Controller
{

    const WEEKLY_AMOUNT = 20;
    const MONTHLY_AMOUNT = 80;
    const YEARLY_AMOUNT = 200;
    const CURRENCY = 'USD';

    public function __construct()
    {
        $this->middleware(['auth', isEmployer::class]);
        $this->middleware(['auth', donotAllowUserToMakePayment::class])->except('subscribe');
    }

    public function subscribe()
    {
        return view('subscription.index');
    }
//update inintiatepaynent methode
    public function initiatePayment(Request $request)
    {
        $plans = [
            'weekly' => [
                'name' => 'weekshib',
                'description' => 'weekly payment',
                'price_id' => 'price_1OqFXWEwtL6xwbTTGTinwpOR', // Replace with your actual Stripe price ID
            ],
            'monthly' => [
                'name' => 'monthly',
                'description' => 'monthly payment',
                'price_id' => 'price_1OqGpbEwtL6xwbTTKLijTUri', // Replace with your actual Stripe price ID
            ],
            'yearly' => [
                'name' => 'yearly',
                'description' => 'yearly payment',
                'price_id' => 'price_1OqFbaEwtL6xwbTTqKIumEha', // Replace with your actual Stripe price ID
            ],
        ];
    
        Stripe::setApiKey(config('services.stripe.secret'));
    
        try {
            $selectedPlan = null;
            if ($request->is('pay/weekly')) {
                $selectedPlan = $plans['weekly'];
                $billingInterval = 'week';
            } elseif ($request->is('pay/monthly')) {
                $selectedPlan = $plans['monthly'];
                $billingInterval = 'month';
            } elseif ($request->is('pay/yearly')) {
                $selectedPlan = $plans['yearly'];
                $billingInterval = 'year';
            }
    
            if ($selectedPlan) {
                $successUrl = URL::signedRoute('payment.success', [
                    'plan' => $selectedPlan['name'],
                ]);
    
                $session = Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price' =>$selectedPlan['price_id'],
                        'quantity' => 1,

                    ]],
                    'mode' => 'subscription',
                    // 'subscription_data' => [
                    //  //   'trial_period_days' => 7, // Example: 7 days trial period
                    //     'billing_cycle_anchor' => strtotime('midnight +' . $billingInterval),
                    // ],
                   
                       
                 
                    'success_url' => $successUrl,
                    'cancel_url' => route('payment.cancel'),
                ]);
   // dd($session);
                $billing_ends=$billingInterval;
                return redirect($session->url);
            }
        } catch (\Exception $e) {
            dd($e);
            return response()->json($e);
        }
    }
    public function paymentSuccess(Request $request) 
    {
       $plan = $request->plan;
       $billingInterval = $request->billing_ends;
        User::where('id', auth()->user()->id)->update([
            'plan' => $plan,
            'billing_ends' => $billingInterval,
            'status' => 'paid'
        ]);
        
        try {
            Mail::to(auth()->user())->queue(new PurchaseMail($plan,$billingInterval));

        }catch (\Exception $e) {
            return response()->json($e);
        }

        return redirect()->route('dashboard')->with('success','Payment was successfully processed');
    }

    public function cancel()
    {
        return redirect()->route('dashboard')->with('error','Payment was unsuccessful!');
    }
    
} 