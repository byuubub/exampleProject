<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSettingsController extends Controller
{
    private function menuItems(): array
    {
        return (new AdminDashboardController)->getMenuItemsPublic(Auth::user()->role);
    }

    public function index()
    {
        $settings = [
            'app_name'             => Setting::get('app_name', 'MedVerse'),
            'app_email'            => Setting::get('app_email', 'info@medverse.id'),
            'app_phone'            => Setting::get('app_phone', '+62 21 1234 5678'),
            'app_address'          => Setting::get('app_address', '123 Healthcare Avenue, Jakarta'),
            'registration_open'    => Setting::get('registration_open', '1'),
            'appointment_lead_days'=> Setting::get('appointment_lead_days', '1'),
            'max_appointments_day' => Setting::get('max_appointments_day', '20'),
            'maintenance_mode'     => Setting::get('maintenance_mode', '0'),
        ];

        $menuItems = $this->menuItems();

        return view('admin.settings', compact('settings', 'menuItems'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name'             => 'required|string|max:100',
            'app_email'            => 'required|email',
            'app_phone'            => 'nullable|string|max:30',
            'app_address'          => 'nullable|string',
            'registration_open'    => 'boolean',
            'appointment_lead_days'=> 'required|integer|min:0|max:30',
            'max_appointments_day' => 'required|integer|min:1|max:200',
            'maintenance_mode'     => 'boolean',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '0');
        }

        return back()->with('success', 'Settings saved successfully.');
    }
}
