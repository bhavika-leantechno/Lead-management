<?php

namespace App\Http\Controllers; // Ensure the correct namespace is used

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    public function sendOtp(Request $request)
{
    try {
        // Validate the email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'data' => $validator->errors(),
            ], 200); // Unprocessable Entity status
        }

        // Get the email input
        $email = $request->input('email');

        // Generate OTP
        $otp = Str::random(6); // You can use a more secure method for generating OTP
        $expiryTime = now()->addMinutes(10); // OTP expiry time (10 minutes)

        // Store OTP and expiry time in the database or cache
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            ['otp' => $otp, 'expiry_time' => $expiryTime]
        );

        // Send OTP to the user's email
        Mail::send([], [], function ($message) use ($email, $otp) {
            $message->to($email)
                    ->subject('Password Reset OTP')
                    ->html("Your OTP for password reset is: $otp");  // Use the `html()` method
        });

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully to your email address.',
            'data' => null
        ]);
    } catch (\Exception $e) {
        // Handle any exceptions that occur
        return response()->json([
            'status' => false,
            'message' => 'An error occurred while sending the OTP.',
            'data' => $e->getMessage(),
        ], 500); // 500 Internal Server Error
    }
}
}
