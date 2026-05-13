<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPatientController extends Controller
{
    private function menuItems(): array
    {
        return (new AdminDashboardController)->getMenuItemsPublic(Auth::user()->role);
    }

    public function index(Request $request)
    {
        $query = Patient::with(['user', 'appointments']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('full_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
                ->orWhere('medical_record_number', 'like', "%{$search}%");
        }

        $patients  = $query->latest()->paginate(20);
        $menuItems = $this->menuItems();

        return view('admin.patients', compact('patients', 'menuItems'));
    }
}
