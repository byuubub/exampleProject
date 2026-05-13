@extends('layouts.admin')
@section('title', 'Examination')
@section('page-title', 'Patient Examination')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 fade-in" x-data="examForm()">

    {{-- Patient Info --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                <i class="fa-solid fa-user text-teal-500 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $appointment->patient->user->full_name ?? 'Patient' }}
                </h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    {{ $appointment->appointment_date->format('d M Y') }} · {{ $appointment->appointment_time }}
                </p>
                @if($appointment->complaint)
                <p class="text-teal-600 dark:text-teal-400 text-sm mt-1">
                    <i class="fa-solid fa-comment-medical mr-1"></i>{{ $appointment->complaint }}
                </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Examination Form --}}
    <form method="POST" action="{{ route('dashboard.examination.store', $appointment->id) }}" class="space-y-5">
        @csrf

        {{-- Diagnosis & Treatment --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="font-semibold text-gray-900 dark:text-white">Clinical Notes</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Diagnosis *</label>
                <textarea name="diagnosis" rows="3" required
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none resize-none @error('diagnosis') border-red-400 @enderror"
                          placeholder="Enter diagnosis..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Treatment Plan</label>
                <textarea name="treatment_plan" rows="2"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none resize-none"
                          placeholder="Treatment or follow-up plan..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Additional Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none resize-none"
                          placeholder="Other clinical notes..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Case Status *</label>
                <select name="case_status" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                    <option value="active">Active / Ongoing</option>
                    <option value="resolved">Resolved</option>
                    <option value="follow_up">Follow Up Required</option>
                </select>
            </div>
        </div>

        {{-- Prescriptions --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white">Prescriptions</h3>
                <button type="button" @click="addMedicine()"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg bg-teal-500 text-white text-sm font-medium hover:bg-teal-600 transition-colors">
                    <i class="fa-solid fa-plus mr-1.5"></i> Add Medicine
                </button>
            </div>

            <div class="space-y-4" id="medicines">
                <template x-for="(med, i) in medicines" :key="i">
                    <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-600 relative">
                        <button type="button" @click="removeMedicine(i)"
                                class="absolute top-3 right-3 p-1 rounded-lg text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                            <i class="fa-solid fa-times text-sm"></i>
                        </button>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Medicine Name *</label>
                                <input type="text" :name="`medicines[${i}][name]`" x-model="med.name" required
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Dosage</label>
                                <input type="text" :name="`medicines[${i}][dosage]`" x-model="med.dosage" placeholder="e.g. 500mg"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Frequency</label>
                                <input type="text" :name="`medicines[${i}][frequency]`" x-model="med.frequency" placeholder="e.g. 3x/day"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Duration</label>
                                <input type="text" :name="`medicines[${i}][duration]`" x-model="med.duration" placeholder="e.g. 5 days"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Instructions</label>
                                <input type="text" :name="`medicines[${i}][instructions]`" x-model="med.instructions" placeholder="e.g. After meals"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none text-sm">
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="medicines.length === 0" class="py-6 text-center text-gray-400 text-sm border border-dashed border-gray-200 dark:border-gray-600 rounded-xl">
                    No prescriptions added. Click "Add Medicine" to add one.
                </div>
            </div>
        </div>

        {{-- Billing --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Billing</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Consultation Fee (Rp) *</label>
                <input type="number" name="bill_amount" required min="0"
                       value="{{ $appointment->doctor->consultation_fee ?? 0 }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                <p class="text-xs text-gray-400 mt-1">Includes consultation and any additional charges</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <a href="{{ route('dashboard.today') }}"
               class="flex-1 py-3.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 text-center font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="flex-1 py-3.5 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold hover:from-teal-600 hover:to-teal-700 transition-all shadow-lg shadow-teal-500/25">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Save Examination
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function examForm() {
    return {
        medicines: [],
        addMedicine() {
            this.medicines.push({ name: '', dosage: '', frequency: '', duration: '', instructions: '' });
        },
        removeMedicine(i) {
            this.medicines.splice(i, 1);
        }
    }
}
</script>
@endpush
