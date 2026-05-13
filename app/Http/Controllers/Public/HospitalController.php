<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Doctor;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    public function index(Request $request)
    {
        $query = Hospital::where('status', 'active');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $hospitals = $query->paginate(12);
        $cities = Hospital::where('status', 'active')->distinct()->pluck('city')->sort()->values();

        return view('public.hospitals.index', compact('hospitals', 'cities'));
    }

    public function show($id)
    {
        $hospital = Hospital::findOrFail($id);
        $doctors  = Doctor::where('hospital_id', $id)
            ->where('status', 'active')
            ->with('specialization')
            ->get();

        return view('public.hospitals.show', compact('hospital', 'doctors'));
    }
}
