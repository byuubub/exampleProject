<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminScheduleController extends Controller
{
    private function menuItems(): array
    {
        return (new AdminDashboardController)->getMenuItemsPublic(Auth::user()->role);
    }

    public function index()
    {
        $schedules = Schedule::with(['doctor.user', 'doctor.hospital'])
            ->latest()->paginate(20);
        $doctors   = Doctor::with('user')->where('status', 'active')->get();
        $menuItems = $this->menuItems();

        return view('admin.schedules', compact('schedules', 'doctors', 'menuItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'doctor_id'   => 'required|exists:doctors,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'quota'       => 'nullable|integer|min:1',
        ]);

        $data['is_active'] = true;

        Schedule::create($data);

        return redirect()->route('admin.schedules.index')->with('success', 'Schedule created.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        // Toggle active via inline button
        if ($request->has('is_active') && count($request->all()) === 2) {
            $schedule->update(['is_active' => $request->boolean('is_active')]);
            return back()->with('success', 'Schedule status updated.');
        }

        $data = $request->validate([
            'doctor_id'   => 'required|exists:doctors,id',
            'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'quota'       => 'nullable|integer|min:1',
        ]);

        $schedule->update($data);

        return redirect()->route('admin.schedules.index')->with('success', 'Schedule updated.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Schedule deleted.');
    }
}
