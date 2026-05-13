<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDoctorController extends Controller
{
    private function menuItems(): array
    {
        return (new AdminDashboardController)->getMenuItemsPublic(Auth::user()->role);
    }

    public function index(Request $request)
    {
        $doctors         = Doctor::with(['user', 'hospital', 'specialization'])->latest()->paginate(12);
        $hospitals       = Hospital::where('status', 'active')->get();
        $specializations = Specialization::orderBy('name')->get();
        $users           = User::whereDoesntHave('doctor')->get();
        $menuItems       = $this->menuItems();

        return view('admin.doctors', compact('doctors', 'hospitals', 'specializations', 'users', 'menuItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'           => 'required|exists:users,id',
            'hospital_id'       => 'required|exists:hospitals,id',
            'specialization_id' => 'required|exists:specializations,id',
            'license_number'    => 'nullable|string|max:100',
            'experience_years'  => 'nullable|integer|min:0',
            'consultation_fee'  => 'nullable|numeric|min:0',
            'bio'               => 'nullable|string',
            'status'            => 'in:active,inactive',
        ]);

        Doctor::create($data);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor added successfully.');
    }

    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'hospital_id'       => 'required|exists:hospitals,id',
            'specialization_id' => 'required|exists:specializations,id',
            'license_number'    => 'nullable|string|max:100',
            'experience_years'  => 'nullable|integer|min:0',
            'consultation_fee'  => 'nullable|numeric|min:0',
            'bio'               => 'nullable|string',
            'status'            => 'in:active,inactive',
        ]);

        $doctor->update($data);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor removed.');
    }
}
