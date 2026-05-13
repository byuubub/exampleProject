<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSpecializationController extends Controller
{
    private function menuItems(): array
    {
        return (new AdminDashboardController)->getMenuItemsPublic(Auth::user()->role);
    }

    public function index()
    {
        $specializations = Specialization::withCount('doctors')->orderBy('name')->paginate(20);
        $menuItems = $this->menuItems();

        return view('admin.specializations', compact('specializations', 'menuItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:specializations,name',
            'description' => 'nullable|string',
        ]);

        Specialization::create($data);

        return redirect()->route('admin.specializations.index')->with('success', 'Specialization added.');
    }

    public function update(Request $request, Specialization $specialization)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $specialization->update($data);

        return redirect()
            ->route('admin.specializations.index')
            ->with('success', 'Specialization updated.');
    }

    public function destroy(Specialization $specialization)
    {
        if ($specialization->doctors()->count() > 0) {
            return back()->with('error', 'Cannot delete: specialization has doctors assigned.');
        }

        $specialization->delete();

        return redirect()->route('admin.specializations.index')->with('success', 'Specialization deleted.');
    }
}
