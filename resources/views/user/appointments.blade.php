@extends('layouts.admin')

@section('title', 'My Appointments')
@section('page-title', 'My Appointments')

@section('content')

<div class="space-y-6 fade-in" x-data="appointmentPage()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Appointments</h2>
            <p class="text-gray-500 dark:text-gray-400">Manage your medical appointments</p>
        </div>
        <button @click="showModal = true" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600 transition-colors">
            <i class="fa-solid fa-plus mr-2"></i> Book Appointment
        </button>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex space-x-2 overflow-x-auto">
        @foreach(['all' => 'All', 'scheduled' => 'Scheduled', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
        <button @click="filter = '{{ $val }}'"
                class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors"
                :class="filter === '{{ $val }}' ? 'bg-teal-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Appointments List --}}
    <div class="space-y-4">
        @forelse($appointments as $appt)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all"
             x-show="filter === 'all' || filter === '{{ $appt->status }}'">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex items-center space-x-4 flex-1">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-teal-100 to-cyan-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user-doctor text-teal-500 text-xl"></i>
                    </div>
                    <div>
                        @if(Auth::user()->role === 'doctor')
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $appt->patient->full_name ?? 'Patient' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Patient</p>
                        @else
                        <p class="font-semibold text-gray-900 dark:text-white">Dr. {{ $appt->doctor->user->full_name ?? 'Doctor' }}</p>
                        <p class="text-sm text-teal-600 dark:text-teal-400">{{ $appt->doctor->specialization->name ?? 'General' }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 items-center">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 px-3 py-1.5 rounded-lg">
                        <i class="fa-solid fa-calendar mr-1.5 text-teal-500"></i>
                        {{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 px-3 py-1.5 rounded-lg">
                        <i class="fa-solid fa-clock mr-1.5 text-teal-500"></i>
                        {{ $appt->appointment_time }}
                    </div>
                    <span class="px-3 py-1.5 rounded-full text-xs font-medium
                                 {{ match($appt->status) {
                                     'scheduled'  => 'bg-blue-100 text-blue-700',
                                     'confirmed'  => 'bg-teal-100 text-teal-700',
                                     'completed'  => 'bg-green-100 text-green-700',
                                     'cancelled'  => 'bg-red-100 text-red-700',
                                     default      => 'bg-gray-100 text-gray-600',
                                 } }}">
                        {{ ucfirst($appt->status) }}
                    </span>
                    @if($appt->status === 'scheduled' && Auth::user()->role === 'patient')
                    <form method="POST" action="{{ route('dashboard.appointments.destroy', $appt->id) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Cancel this appointment?')"
                                class="px-3 py-1.5 rounded-lg text-red-500 border border-red-200 hover:bg-red-50 text-xs font-medium transition-colors">
                            <i class="fa-solid fa-xmark mr-1"></i> Cancel
                        </button>
                    </form>
                    @endif
                    @if($appt->status === 'confirmed' && Auth::user()->role === 'doctor')
                    <a href="{{ route('dashboard.examination', $appt->id) }}"
                       class="px-3 py-1.5 rounded-lg bg-teal-500 text-white text-xs font-medium hover:bg-teal-600 transition-colors">
                        <i class="fa-solid fa-stethoscope mr-1"></i> Examine
                    </a>
                    @endif
                </div>
            </div>
            @if($appt->complaint)
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Complaint:</span> {{ $appt->complaint }}
                </p>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl py-16 text-center text-gray-400 shadow-sm">
            <i class="fa-solid fa-calendar-xmark text-4xl mb-3"></i>
            <p class="font-medium">No appointments found</p>
            <button @click="showModal = true" class="mt-4 px-4 py-2 rounded-xl bg-teal-500 text-white text-sm font-medium hover:bg-teal-600">
                Book Your First Appointment
            </button>
        </div>
        @endforelse
    </div>

    @if($appointments->hasPages())
    <div>{{ $appointments->links() }}</div>
    @endif

    {{-- Book Appointment Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>
        <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between p-6 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Book Appointment</h3>
                <button @click="showModal = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('dashboard.appointments.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Doctor *</label>
                    <select name="doctor_id" required
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                        <option value="">Select a Doctor</option>
                        @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">
                            Dr. {{ $doctor->user->full_name ?? 'N/A' }} — {{ $doctor->specialization->name ?? '' }}
                            @if($doctor->consultation_fee)
                            (Rp {{ number_format($doctor->consultation_fee, 0, ',', '.') }})
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date *</label>
                    <input type="date" name="appointment_date" required min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Time *</label>
                    <input type="time" name="appointment_time" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Complaint *</label>
                    <textarea name="complaint" rows="3" required
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none resize-none"
                              placeholder="Describe your symptoms..."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" @click="showModal = false"
                            class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600">
                        Book Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function appointmentPage() {
    return { filter: 'all', showModal: {{ $errors->any() ? 'true' : 'false' }} }
}
</script>
@endpush
