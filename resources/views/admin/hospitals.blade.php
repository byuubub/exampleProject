@extends('layouts.admin')

@section('title', 'Hospital Management')
@section('page-title', 'Hospital Management')

@section('content')

<div class="space-y-6 fade-in" x-data="hospitalManagement()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Hospitals</h2>
            <p class="text-gray-500 dark:text-gray-400">Manage all hospitals in the network</p>
        </div>
        <button @click="openModal()" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600 transition-colors">
            <i class="fa-solid fa-plus mr-2"></i>
            Add Hospital
        </button>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" x-model="search" placeholder="Search hospitals..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
            </div>
            <select x-model="statusFilter"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-left">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hospital</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Location</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Contact</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($hospitals as $hospital)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                    x-show="filterHospital('{{ strtolower($hospital->name.' '.$hospital->city) }}', '{{ $hospital->status }}')">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-hospital text-teal-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $hospital->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 md:hidden">{{ $hospital->city }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <p class="text-gray-700 dark:text-gray-300">{{ $hospital->city }}</p>
                        @if($hospital->address)
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($hospital->address, 40) }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        @if($hospital->phone)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <i class="fa-solid fa-phone mr-1 text-teal-500"></i> {{ $hospital->phone }}
                        </p>
                        @endif
                        @if($hospital->email)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <i class="fa-solid fa-envelope mr-1 text-teal-500"></i> {{ $hospital->email }}
                        </p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                     {{ $hospital->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($hospital->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button @click="editHospital({{ $hospital->toJson() }})"
                                    class="p-2 rounded-lg text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button @click="confirmDelete({{ $hospital->id }}, '{{ $hospital->name }}')"
                                    class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center text-gray-400">
                            <i class="fa-solid fa-hospital text-4xl mb-3"></i>
                            <p class="font-medium">No hospitals found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($hospitals->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $hospitals->links() }}
        </div>
        @endif
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl"
             @click.stop>
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white"
                    x-text="editMode ? 'Edit Hospital' : 'Add Hospital'"></h3>
                <button @click="showModal = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form :action="editMode ? `/admin/hospitals/${form.id}` : '{{ route('admin.hospitals.store') }}'"
                  method="POST" class="p-6 space-y-4">
                @csrf
                <span x-show="editMode" x-cloak>
                    <input type="hidden" name="_method" value="PUT">
                </span>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Hospital Name *</label>
                        <input type="text" name="name" x-model="form.name" required
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">City *</label>
                        <input type="text" name="city" x-model="form.city" required
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                        <select name="status" x-model="form.status"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                        <input type="text" name="address" x-model="form.address"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone</label>
                        <input type="text" name="phone" x-model="form.phone"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                        <input type="email" name="email" x-model="form.email"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600 transition-colors">
                        <span x-text="editMode ? 'Update' : 'Save'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirm Modal --}}
    <div x-show="showDeleteModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showDeleteModal = false"></div>
        <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 text-center" @click.stop>
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-trash text-red-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Delete Hospital?</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">
                Are you sure you want to delete <strong x-text="deleteTarget.name"></strong>? This action cannot be undone.
            </p>
            <form :action="`/admin/hospitals/${deleteTarget.id}`" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex space-x-3">
                    <button type="button" @click="showDeleteModal = false"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600 transition-colors">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
function hospitalManagement() {
    return {
        search: '',
        statusFilter: '',
        showModal: false,
        showDeleteModal: false,
        editMode: false,
        deleteTarget: { id: null, name: '' },
        form: { id: null, name: '', city: '', address: '', phone: '', email: '', status: 'active' },

        openModal() {
            this.editMode = false;
            this.form = { id: null, name: '', city: '', address: '', phone: '', email: '', status: 'active' };
            this.showModal = true;
        },

        editHospital(hospital) {
            this.editMode = true;
            this.form = { ...hospital };
            this.showModal = true;
        },

        confirmDelete(id, name) {
            this.deleteTarget = { id, name };
            this.showDeleteModal = true;
        },

        filterHospital(text, status) {
            const matchSearch = !this.search || text.includes(this.search.toLowerCase());
            const matchStatus = !this.statusFilter || status === this.statusFilter;
            return matchSearch && matchStatus;
        }
    }
}
</script>
@endpush
