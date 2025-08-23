<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class OtpController extends Controller
{
    // Show email input form
    public function showEmailForm()
    {
        return view('auth.email-request');
    }

    // Send OTP after email is entered
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email not found in our system.']);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Store in session (or DB for stronger security)
        Session::put('otp', $otp);
        Session::put('otp_email', $user->email);

        // Send email
        Mail::raw("Your OTP is: {$otp}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your OTP Code');
        });

        return redirect()->route('otp.verify.form')->with('success', 'OTP sent to your email!');
    }

    // Show OTP form
    public function showOtpForm()
    {
        return view('auth.otp-verify');
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric'
        ]);

        if (Session::get('otp') == $request->otp) {
            Session::put('otp_verified', true);
            return redirect()->route('profile.edit');
        }

        return back()->withErrors(['otp' => 'Invalid OTP. Try again.']);
    }
}
