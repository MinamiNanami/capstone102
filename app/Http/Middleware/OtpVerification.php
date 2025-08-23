<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OtpVerification
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user already verified OTP
        if (!$request->session()->get('otp_verified')) {
            return redirect()->route('otp.verify'); // Redirect to OTP page
        }

        return $next($request);
    }
}
