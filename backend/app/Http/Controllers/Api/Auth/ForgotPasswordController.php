<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Service\Api\Auth\ForgotPasswordService;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    protected ForgotPasswordService $service;

    public function __construct(ForgotPasswordService $service)
    {
        $this->service = $service;
    }

    /**
     * STEP 1: Send OTP to phone
     */
    public function checkPhone(Request $request)
    {
        return $this->service->checkPhone($request);
    }

    /**
     * STEP 2: Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        return $this->service->verifyOtp($request);
    }

    /**
     * STEP 3: Reset password
     */
    public function resetPasswordByPhone(Request $request)
    {
        return $this->service->resetPasswordByPhone($request);
    }
}