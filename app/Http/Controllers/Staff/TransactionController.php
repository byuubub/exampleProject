<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
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

        $query = Transaction::with(['bill.patient.user', 'bill.appointment.doctor.user', 'processedBy'])
            ->when($hospitalId, fn($q) => $q->whereHas(
                'bill.appointment.doctor', fn($d) => $d->where('hospital_id', $hospitalId)
            ));

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        $transactions = $query->latest()->paginate(20);

        $todayTotal = Transaction::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        $menuItems = $this->menuItems();

        return view('staff.transactions', compact('transactions', 'todayTotal', 'menuItems'));
    }
}
