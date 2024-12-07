<?php
namespace App\Http\Controllers; // Ensure the correct namespace is used

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OtpVerificationController extends Controller
{
    public function verifyOtp(Request $request)
    {
      
        // Validate the input OTP
        $request->validate([
            'email' => 'required|email|exists:users,email', // Ensure the email exists in the database
            'otp' => 'required|string|size:6', // OTP should be exactly 6 characters
        ]);

        $email = $request->input('email');
        $otp = $request->input('otp');
        
        // Retrieve the OTP from the database
        $resetData = DB::table('password_resets')->where('email', $email)->first();

        // Check if the OTP exists and is not expired
        if (!$resetData || $resetData->otp !== $otp || $resetData->expiry_time < now()) {
            return response()->json([
                'message' => 'Invalid or expired OTP.',
            ], 400);
        }

        // OTP is valid, allow user to reset password
        return response()->json([
            'message' => 'OTP verified successfully. You can now reset your password.',
        ]);
    }
}
