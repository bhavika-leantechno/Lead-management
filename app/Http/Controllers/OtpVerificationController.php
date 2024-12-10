<?php
namespace App\Http\Controllers; // Ensure the correct namespace is used

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OtpVerificationController extends Controller
{
    public function verifyOtp(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
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
