<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminStaffController extends Controller
{
    private function menuItems(): array
    {
        return (new AdminDashboardController)->getMenuItemsPublic(Auth::user()->role);
    }

    public function index()
    {
        $staff     = User::where('role', 'staff')->with('hospital')->latest()->paginate(15);
        $hospitals = Hospital::where('status', 'active')->get();
        $menuItems = $this->menuItems();

        return view('admin.staff', compact('staff', 'hospitals', 'menuItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'   => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'hospital_id' => 'required|exists:hospitals,id',
            'phone'       => 'nullable|string|max:20',
        ]);

        $data['role']     = 'staff';
        $data['password'] = Hash::make('password123'); // default password

        User::create($data);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff added. Default password: password123');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'full_name'   => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'hospital_id' => 'required|exists:hospitals,id',
            'phone'       => 'nullable|string|max:20',
        ]);

        $user->update($data);

        return redirect()->route('admin.staff.index')->with('success', 'Staff updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff removed.');
    }
}
