@extends('layouts.admin')

@section('title', 'Staff Management')
@section('page-title', 'Staff Management')

@section('content')

<div class="space-y-6 fade-in" x-data="staffManagement()">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Staff</h2>
            <p class="text-gray-500 dark:text-gray-400">Manage hospital staff members</p>
        </div>
        <button @click="openModal()"
                class="inline-flex items-center px-4 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600 transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Add Staff
        </button>
    </div>

    {{-- Search --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm">
        <div class="relative max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" x-model="search" placeholder="Search staff..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
        </div>
    </div>

    {{-- Staff Cards --}}
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($staff as $member)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all"
             x-show="!search || '{{ strtolower($member->user->full_name ?? '') }}'.includes(search.toLowerCase())">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-100 to-teal-100 flex items-center justify-center">
                        <span class="text-teal-600 text-lg font-bold">
                            {{ strtoupper(substr($member->user->full_name ?? 'S', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $member->user->full_name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->user->email ?? '' }}</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium
                             {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($member->status ?? 'active') }}
                </span>
            </div>

            <div class="space-y-2 mb-4">
                @if($member->hospital)
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <i class="fa-solid fa-hospital text-teal-400 w-5"></i>
                    <span>{{ $member->hospital->name }}</span>
                </div>
                @endif
                @if($member->user->phone)
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <i class="fa-solid fa-phone text-teal-400 w-5"></i>
                    <span>{{ $member->user->phone }}</span>
                </div>
                @endif
                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                    <i class="fa-solid fa-calendar text-teal-400 w-5"></i>
                    <span>Joined {{ $member->created_at->format('M Y') }}</span>
                </div>
            </div>

            <div class="flex space-x-2 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button @click="editStaff({{ $member->toJson() }})"
                        class="flex-1 py-2 rounded-lg text-blue-500 border border-blue-200 hover:bg-blue-50 dark:border-blue-800 dark:hover:bg-blue-900/30 text-sm font-medium transition-colors">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                </button>
                <button @click="confirmDelete({{ $member->id }}, '{{ $member->user->full_name ?? 'this staff' }}')"
                        class="flex-1 py-2 rounded-lg text-red-500 border border-red-200 hover:bg-red-50 dark:border-red-800 dark:hover:bg-red-900/30 text-sm font-medium transition-colors">
                    <i class="fa-solid fa-trash mr-1"></i> Remove
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 text-gray-400">
            <i class="fa-solid fa-users text-4xl mb-3"></i>
            <p class="font-medium">No staff members found</p>
        </div>
        @endforelse
    </div>

    @if(isset($staff) && method_exists($staff, 'hasPages') && $staff->hasPages())
    <div>{{ $staff->links() }}</div>
    @endif

    {{-- Add/Edit Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl" @click.stop>
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white"
                    x-text="editMode ? 'Edit Staff' : 'Add Staff'"></h3>
                <button @click="showModal = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form :action="editMode ? `/admin/staff/${form.id}` : '{{ route('admin.staff.store') }}'"
                  method="POST" class="p-6 space-y-4">
                @csrf
                <input x-show="editMode" type="hidden" name="_method" value="PUT">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">User Account *</label>
                    <select name="user_id" x-model="form.user_id" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                        <option value="">Select User (role: staff)</option>
                        @foreach($staffUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->full_name }} — {{ $u->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Hospital *</label>
                    <select name="hospital_id" x-model="form.hospital_id" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                        <option value="">Select Hospital</option>
                        @foreach($hospitals as $hospital)
                        <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                    <select name="status" x-model="form.status"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600">
                        <span x-text="editMode ? 'Update' : 'Add'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirm --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showDeleteModal = false"></div>
        <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 text-center" @click.stop>
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-trash text-red-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Remove Staff?</h3>
            <p class="text-gray-500 mb-6">Remove <strong x-text="deleteTarget.name"></strong> from staff?</p>
            <form :action="`/admin/staff/${deleteTarget.id}`" method="POST">
                @csrf @method('DELETE')
                <div class="flex space-x-3">
                    <button type="button" @click="showDeleteModal = false"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600">Remove</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function staffManagement() {
    return {
        search: '', showModal: false, showDeleteModal: false, editMode: false,
        deleteTarget: { id: null, name: '' },
        form: { id: null, user_id: '', hospital_id: '', status: 'active' },
        openModal() { this.editMode = false; this.form = { id: null, user_id: '', hospital_id: '', status: 'active' }; this.showModal = true; },
        editStaff(s) { this.editMode = true; this.form = { ...s }; this.showModal = true; },
        confirmDelete(id, name) { this.deleteTarget = { id, name }; this.showDeleteModal = true; }
    }
}
</script>
@endpush
