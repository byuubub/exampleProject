<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlinePaymentController extends Controller
{
    public function show(Bill $bill)
    {
        $user    = Auth::user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        if ($bill->patient_id !== $patient->id) abort(403);
        if ($bill->status === 'paid') return redirect()->route('dashboard.bills')->with('error', 'Bill already paid.');

        $bill->load('appointment.doctor.user');
        $menuItems = (new UserDashboardController)->getMenuItems($user->role);

        return view('user.payment', compact('bill', 'menuItems'));
    }

    public function process(Request $request, Bill $bill)
    {
        $user    = Auth::user();
        $patient = Patient::where('user_id', $user->id)->firstOrFail();

        if ($bill->patient_id !== $patient->id) abort(403);

        $data = $request->validate([
            'payment_method' => 'required|in:transfer,va,ewallet',
        ]);

        Transaction::create([
            'bill_id'        => $bill->id,
            'amount'         => $bill->amount,
            'payment_method' => $data['payment_method'],
            'processed_by'   => $user->id,
            'status'         => 'completed',
            'notes'          => 'Online payment via ' . $data['payment_method'],
        ]);

        $bill->update(['status' => 'paid']);

        return redirect()->route('dashboard.bills')
            ->with('success', 'Payment of Rp ' . number_format($bill->amount, 0, ',', '.') . ' successful!');
    }
}
