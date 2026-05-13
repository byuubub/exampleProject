@extends('layouts.admin')

@section('title', 'Schedule Management')
@section('page-title', 'Schedule Management')

@section('content')

<div class="space-y-6 fade-in" x-data="scheduleManagement()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Schedules</h2>
            <p class="text-gray-500 dark:text-gray-400">Manage doctor practice schedules</p>
        </div>
        <button @click="openModal()" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600 transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Add Schedule
        </button>
    </div>

    {{-- Search --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm">
        <div class="relative max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" x-model="search" placeholder="Search by doctor name..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-left">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Doctor</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Day</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Time</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Quota</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($schedules as $schedule)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                    x-show="!search || '{{ strtolower($schedule->doctor->user->full_name ?? '') }}'.includes(search.toLowerCase())">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-user-doctor text-teal-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $schedule->doctor->user->full_name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 md:hidden">{{ $schedule->day_of_week }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400">
                            {{ $schedule->day_of_week }}
                        </span>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fa-solid fa-clock mr-1.5 text-teal-400"></i>
                            {{ $schedule->start_time }} – {{ $schedule->end_time }}
                        </div>
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        <span class="text-gray-700 dark:text-gray-300">{{ $schedule->quota ?? '-' }} patients</span>
                    </td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('admin.schedules.update', $schedule->id) }}" class="inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $schedule->is_active ? 0 : 1 }}">
                            <button type="submit"
                                    class="flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-medium transition-colors
                                           {{ $schedule->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                <i class="fa-solid {{ $schedule->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                <span>{{ $schedule->is_active ? 'Active' : 'Inactive' }}</span>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button @click="editSchedule({{ $schedule->toJson() }})"
                                    class="p-2 rounded-lg text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button @click="confirmDelete({{ $schedule->id }}, '{{ $schedule->doctor->user->full_name ?? 'this schedule' }}')"
                                    class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                        <i class="fa-solid fa-calendar-xmark text-4xl mb-3"></i>
                        <p>No schedules found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($schedules->hasPages())
        <div class="px-6 py-4 border-t dark:border-gray-700">{{ $schedules->links() }}</div>
        @endif
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl" @click.stop>
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editMode ? 'Edit Schedule' : 'Add Schedule'"></h3>
                <button @click="showModal = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form :action="editMode ? `/admin/schedules/${form.id}` : '{{ route('admin.schedules.store') }}'"
                  method="POST" class="p-6 space-y-4">
                @csrf
                <input x-show="editMode" type="hidden" name="_method" value="PUT">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Doctor *</label>
                    <select name="doctor_id" x-model="form.doctor_id" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                        <option value="">Select Doctor</option>
                        @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->user->full_name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Day *</label>
                    <select name="day_of_week" x-model="form.day_of_week" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                        <option value="">Select Day</option>
                        @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                        <option value="{{ $day }}">{{ $day }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Start Time *</label>
                        <input type="time" name="start_time" x-model="form.start_time" required
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">End Time *</label>
                        <input type="time" name="end_time" x-model="form.end_time" required
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Patient Quota</label>
                    <input type="number" name="quota" x-model="form.quota" min="1"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600">
                        <span x-text="editMode ? 'Update' : 'Save'"></span>
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
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Delete Schedule?</h3>
            <p class="text-gray-500 mb-6">Remove schedule for <strong x-text="deleteTarget.name"></strong>?</p>
            <form :action="`/admin/schedules/${deleteTarget.id}`" method="POST">
                @csrf @method('DELETE')
                <div class="flex space-x-3">
                    <button type="button" @click="showDeleteModal = false"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600">Cancel</button>
                    <button type="submit" class="flex-1 py-2.5 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600">Delete</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function scheduleManagement() {
    return {
        search: '', showModal: false, showDeleteModal: false, editMode: false,
        deleteTarget: { id: null, name: '' },
        form: { id: null, doctor_id: '', day_of_week: '', start_time: '', end_time: '', quota: '' },
        openModal() { this.editMode = false; this.form = { id: null, doctor_id: '', day_of_week: '', start_time: '', end_time: '', quota: '' }; this.showModal = true; },
        editSchedule(s) { this.editMode = true; this.form = { ...s }; this.showModal = true; },
        confirmDelete(id, name) { this.deleteTarget = { id, name }; this.showDeleteModal = true; }
    }
}
</script>
@endpush
