<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Doctor;
use App\Models\Specialization;

class HomeController extends Controller
{
    public function index()
    {
        $featuredHospitals = Hospital::where('status', 'active')
            ->limit(3)
            ->get()
            ->map(fn($h) => [
                'name'  => $h->name,
                'city'  => $h->city,
                'image' => $h->image_url ?? 'https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=800',
            ])
            ->toArray();

        // Fallback if no hospitals in DB yet
        if (empty($featuredHospitals)) {
            $featuredHospitals = [
                ['name' => 'MedVerse Central Hospital', 'city' => 'Jakarta',  'image' => 'https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=800'],
                ['name' => 'MedVerse Medical Center',   'city' => 'Surabaya', 'image' => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=800'],
                ['name' => 'MedVerse Health Institute',  'city' => 'Bandung',  'image' => 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=800'],
            ];
        }

        $specializations = [
            ['name' => 'General Medicine', 'icon' => 'fa-stethoscope',   'doctors' => 45, 'color' => 'from-teal-400 to-teal-500'],
            ['name' => 'Cardiology',       'icon' => 'fa-heart-pulse',   'doctors' => 12, 'color' => 'from-rose-400 to-rose-500'],
            ['name' => 'Neurology',        'icon' => 'fa-brain',         'doctors' => 8,  'color' => 'from-purple-400 to-purple-500'],
            ['name' => 'Pediatrics',       'icon' => 'fa-baby',          'doctors' => 15, 'color' => 'from-cyan-400 to-cyan-500'],
            ['name' => 'Orthopedics',      'icon' => 'fa-bone',          'doctors' => 10, 'color' => 'from-amber-400 to-amber-500'],
            ['name' => 'Ophthalmology',    'icon' => 'fa-eye',           'doctors' => 7,  'color' => 'from-blue-400 to-blue-500'],
        ];

        return view('public.home', compact('featuredHospitals', 'specializations'));
    }

    public function about()
    {
        $team = [
            ['name' => 'Dr. Sarah Chen',       'role' => 'Chief Medical Officer',  'image' => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400'],
            ['name' => 'Michael Rodriguez',    'role' => 'Chief Executive Officer', 'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400'],
            ['name' => 'Dr. James Wilson',     'role' => 'Head of Operations',      'image' => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=400'],
            ['name' => 'Lisa Thompson',        'role' => 'Chief Technology Officer','image' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400'],
        ];

        return view('public.about', compact('team'));
    }
}
