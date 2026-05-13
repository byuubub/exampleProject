<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminHospitalController extends Controller
{
    private function menuItems(): array
    {
        $role = Auth::user()->role;
        return app(AdminDashboardController::class)->getMenuItemsPublic($role);
    }

    public function index(Request $request)
    {
        $hospitals = Hospital::latest()->paginate(15);
        $menuItems = (new AdminDashboardController)->getMenuItemsPublic(Auth::user()->role);

        return view('admin.hospitals', compact('hospitals', 'menuItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'city'    => 'required|string|max:100',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'status'  => 'in:active,inactive',
        ]);

        Hospital::create($data);

        return redirect()->route('admin.hospitals.index')
            ->with('success', 'Hospital added successfully.');
    }

    public function update(Request $request, Hospital $hospital)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'city'    => 'required|string|max:100',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'status'  => 'in:active,inactive',
        ]);

        $hospital->update($data);

        return redirect()->route('admin.hospitals.index')
            ->with('success', 'Hospital updated successfully.');
    }

    public function destroy(Hospital $hospital)
    {
        $hospital->delete();

        return redirect()->route('admin.hospitals.index')
            ->with('success', 'Hospital deleted successfully.');
    }
}
