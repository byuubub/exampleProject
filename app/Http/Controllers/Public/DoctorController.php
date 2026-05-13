<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\Schedule;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::with(['user', 'hospital', 'specialization'])
            ->where('status', 'active');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('full_name', 'like', "%{$search}%"))
                  ->orWhereHas('specialization', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('spec')) {
            $query->where('specialization_id', $request->spec);
        }

        $doctors         = $query->paginate(12);
        $specializations = Specialization::orderBy('name')->get();

        return view('public.doctors.index', compact('doctors', 'specializations'));
    }

    public function show($id)
    {
        $doctor    = Doctor::with(['user', 'hospital', 'specialization'])->findOrFail($id);
        $schedules = Schedule::where('doctor_id', $id)->where('is_active', true)->get();

        return view('public.doctors.show', compact('doctor', 'schedules'));
    }
}
