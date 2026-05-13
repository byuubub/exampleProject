<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AdminUserController extends Controller
{
    private function menuItems(): array
    {
        return (new AdminDashboardController)->getMenuItemsPublic(Auth::user()->role);
    }

    public function index(Request $request)
    {
        $users     = User::with('hospital')->latest()->paginate(20);
        $hospitals = Hospital::where('status', 'active')->get();
        $menuItems = $this->menuItems();

        return view('admin.users', compact('users', 'hospitals', 'menuItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'   => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:8',
            'role'        => 'required|in:super_admin,admin_rs,staff,doctor,patient',
            'hospital_id' => 'nullable|exists:hospitals,id',
            'phone'       => 'nullable|string|max:20',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        // Handle password reset action
        if ($request->input('action') === 'reset_password') {
            Password::sendResetLink(['email' => $user->email]);
            return back()->with('success', 'Password reset link sent to ' . $user->email);
        }

        $data = $request->validate([
            'full_name'   => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'role'        => 'required|in:super_admin,admin_rs,staff,doctor,patient',
            'hospital_id' => 'nullable|exists:hospitals,id',
            'phone'       => 'nullable|string|max:20',
        ]);

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
