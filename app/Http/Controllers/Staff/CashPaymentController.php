<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashPaymentController extends Controller
{
    private function menuItems(): array
    {
        return [
            ['icon' => 'fa-solid fa-gauge',          'label' => 'Dashboard',     'path' => '/staff'],
            ['icon' => 'fa-solid fa-user-plus',      'label' => 'Registration',  'path' => '/staff/registration'],
            ['icon' => 'fa-solid fa-clock',          'label' => 'Queue',         'path' => '/staff/queue'],
            ['icon' => 'fa-solid fa-calendar-check', 'label' => 'Appointments',  'path' => '/staff/appointments'],
            ['icon' => 'fa-solid fa-credit-card',    'label' => 'Payment',       'path' => '/staff/payment'],
            ['icon' => 'fa-solid fa-receipt',        'label' => 'Transactions',  'path' => '/staff/transactions'],
        ];
    }

    public function index(Request $request)
    {
        $hospitalId = Auth::user()->hospital_id;

        $query = Bill::with(['patient.user', 'appointment.doctor.user'])
            ->where('status', 'pending')
            ->when($hospitalId, fn($q) => $q->whereHas(
                'appointment.doctor', fn($d) => $d->where('hospital_id', $hospitalId)
            ));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient.user', fn($q) => $q->where('full_name', 'like', "%{$search}%"));
        }

        $bills     = $query->latest()->paginate(20);
        $menuItems = $this->menuItems();

        return view('staff.payment', compact('bills', 'menuItems'));
    }

    public function process(Request $request)
    {
        $data = $request->validate([
            'bill_id'        => 'required|exists:bills,id',
            'payment_method' => 'required|in:cash,qris,bank_transfer,insurance,debit',
            'amount_paid'    => 'required|numeric|min:0',
            'notes'          => 'nullable|string',
        ]);

        $bill = Bill::findOrFail($data['bill_id']);

        // Create transaction
        Transaction::create([
            'bill_id'        => $bill->id,
            'amount'         => $bill->amount,
            'payment_method' => $data['payment_method'],
            'processed_by'   => Auth::id(),
            'status'         => 'completed',
            'notes'          => $data['notes'] ?? null,
        ]);

        $bill->update(['status' => 'paid']);

        return redirect()->route('staff.transactions')
            ->with('success', 'Payment of Rp ' . number_format($bill->amount, 0, ',', '.') . ' processed successfully.');
    }
}
