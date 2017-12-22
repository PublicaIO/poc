<?php

namespace App\Http\Controllers;

use App\Kyc;
use Illuminate\Http\Request;
use Auth;

class KycController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $kyc = Auth::user()->kyc;
        return view('kyc.upload', compact('kyc'));
    }

    /**
     * Store kyc images and create DB entry
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {
        $request->validate([
            'passport'             => 'required|image',
            'utility_bill'         => 'required|image',
            'selfie_with_passport' => 'required|image',
        ]);

        $passport = $request->file('passport');
        $utilityBill = $request->file('utility_bill');
        $selfieWithPassport = $request->file('selfie_with_passport');

        if (Auth::user()->kyc) {
            $kyc = Auth::user()->kyc;
        } else {
            $kyc = new Kyc();
        }

        $kyc->passport = $passport->storeAs('kyc/' . Auth::id(), $passport->getClientOriginalName(), 'public');
        $kyc->utility_bill = $utilityBill->storeAs('kyc/' . Auth::id(), $utilityBill->getClientOriginalName(), 'public');
        $kyc->selfie_with_passport = $selfieWithPassport->storeAs('kyc/' . Auth::id(), $selfieWithPassport->getClientOriginalName(), 'public');
        $kyc->verification_passed = 1;

        Auth::user()->kyc()->save($kyc);

        return ['success' => true];
    }
}
