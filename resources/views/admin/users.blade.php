@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')

<div class="space-y-6 fade-in" x-data="userManagement()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Users</h2>
            <p class="text-gray-500 dark:text-gray-400">Manage all system users</p>
        </div>
        <button @click="openModal()"
                class="inline-flex items-center px-4 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600 transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Add User
        </button>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" x-model="search" placeholder="Search by name or email..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
            </div>
            <select x-model="roleFilter"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                <option value="">All Roles</option>
                <option value="super_admin">Super Admin</option>
                <option value="admin_rs">Admin RS</option>
                <option value="staff">Staff</option>
                <option value="doctor">Doctor</option>
                <option value="patient">Patient</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-left">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Email</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Hospital</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Joined</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                    x-show="filterUser('{{ strtolower($user->full_name.' '.$user->email) }}', '{{ $user->role }}')">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-teal-400 to-cyan-500 flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-sm font-bold">{{ strtoupper(substr($user->full_name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $user->full_name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 md:hidden">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 hidden md:table-cell">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                            {{ match($user->role) {
                                'super_admin' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                'admin_rs'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'staff'       => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400',
                                'doctor'      => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                default       => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            } }}">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 hidden lg:table-cell">
                        {{ $user->hospital->name ?? '—' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 hidden lg:table-cell">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-1" x-data="{ open: false }">
                            <button @click="editUser({{ $user->toJson() }})"
                                    class="p-2 rounded-lg text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button @click="sendReset('{{ $user->id }}', '{{ $user->full_name }}')"
                                    class="p-2 rounded-lg text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/30"
                                    title="Reset Password">
                                <i class="fa-solid fa-key"></i>
                            </button>
                            @if($user->id !== Auth::id())
                            <button @click="confirmDelete({{ $user->id }}, '{{ $user->full_name }}')"
                                    class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                        <i class="fa-solid fa-users text-4xl mb-3"></i>
                        <p>No users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
        <div class="px-6 py-4 border-t dark:border-gray-700">{{ $users->links() }}</div>
        @endif
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white"
                    x-text="editMode ? 'Edit User' : 'Add User'"></h3>
                <button @click="showModal = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form :action="editMode ? `/admin/users/${form.id}` : '{{ route('admin.users.store') }}'"
                  method="POST" class="p-6 space-y-4">
                @csrf
                <input x-show="editMode" type="hidden" name="_method" value="PUT">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name *</label>
                    <input type="text" name="full_name" x-model="form.full_name" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email *</label>
                    <input type="email" name="email" x-model="form.email" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone</label>
                    <input type="text" name="phone" x-model="form.phone"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Role *</label>
                        <select name="role" x-model="form.role" required
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                            <option value="patient">Patient</option>
                            <option value="doctor">Doctor</option>
                            <option value="staff">Staff</option>
                            <option value="admin_rs">Admin RS</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Hospital</label>
                        <select name="hospital_id" x-model="form.hospital_id"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                            <option value="">None</option>
                            @foreach($hospitals as $hospital)
                            <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div x-show="!editMode">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                    <input type="password" name="password" x-model="form.password"
                           :required="!editMode"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none"
                           placeholder="Minimum 8 characters">
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600">
                        <span x-text="editMode ? 'Update' : 'Create'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reset Password Confirm --}}
    <div x-show="showResetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showResetModal = false"></div>
        <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 text-center" @click.stop>
            <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-key text-amber-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Reset Password?</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">
                Send password reset email to <strong x-text="resetTarget.name"></strong>?
            </p>
            <form method="POST" :action="`/admin/users/${resetTarget.id}/reset-password`">
                @csrf
                <div class="flex space-x-3">
                    <button type="button" @click="showResetModal = false"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl bg-amber-500 text-white font-medium hover:bg-amber-600">Send</button>
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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Delete User?</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">
                Permanently delete <strong x-text="deleteTarget.name"></strong>? This cannot be undone.
            </p>
            <form :action="`/admin/users/${deleteTarget.id}`" method="POST">
                @csrf @method('DELETE')
                <div class="flex space-x-3">
                    <button type="button" @click="showDeleteModal = false"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600">Delete</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function userManagement() {
    return {
        search: '', roleFilter: '',
        showModal: false, showDeleteModal: false, showResetModal: false,
        editMode: false,
        deleteTarget: { id: null, name: '' },
        resetTarget: { id: null, name: '' },
        form: { id: null, full_name: '', email: '', phone: '', role: 'patient', hospital_id: '', password: '' },

        openModal() {
            this.editMode = false;
            this.form = { id: null, full_name: '', email: '', phone: '', role: 'patient', hospital_id: '', password: '' };
            this.showModal = true;
        },
        editUser(u) { this.editMode = true; this.form = { ...u }; this.showModal = true; },
        confirmDelete(id, name) { this.deleteTarget = { id, name }; this.showDeleteModal = true; },
        sendReset(id, name) { this.resetTarget = { id, name }; this.showResetModal = true; },
        filterUser(text, role) {
            const matchSearch = !this.search || text.includes(this.search.toLowerCase());
            const matchRole   = !this.roleFilter || role === this.roleFilter;
            return matchSearch && matchRole;
        }
    }
}
</script>
@endpush
