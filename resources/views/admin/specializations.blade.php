@extends('layouts.admin')

@section('title', 'Specialization Management')
@section('page-title', 'Specialization Management')

@section('content')

    <div class="space-y-6 fade-in" x-data="specManagement()">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Specializations</h2>
                <p class="text-gray-500 dark:text-gray-400">Manage medical specializations</p>
            </div>
            <button @click="openModal()"
                class="inline-flex items-center px-4 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600">
                <i class="fa-solid fa-plus mr-2"></i> Add Specialization
            </button>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($specializations as $spec)
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                            <i class="fa-solid fa-stethoscope text-teal-500 text-lg"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $spec->name }}</p>
                            <p class="text-sm text-gray-500">{{ $spec->doctors_count ?? 0 }} doctor(s)</p>
                        </div>
                    </div>
                    <div class="flex space-x-1">
                        <button @click="editSpec({{ $spec->toJson() }})"
                            class="p-2 rounded-lg text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button @click="confirmDelete({{ $spec->id }}, '{{ $spec->name }}')"
                            class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-3 py-16 text-center text-gray-400">
                    <i class="fa-solid fa-stethoscope text-4xl mb-3"></i>
                    <p>No specializations found</p>
                </div>
            @endforelse
        </div>

        {{-- Modal --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>
            <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6" @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-semibold text-gray-900 dark:text-white"
                        x-text="editMode ? 'Edit Specialization' : 'Add Specialization'"></h3>
                    <button @click="showModal = false"
                        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-400">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form
                    x-bind:action="editMode ? `/admin/specializations/${form.id}` : '{{ route('admin.specializations.store') }}'"
                    method="POST" class="space-y-4">

                    @csrf

                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <input type="text" name="name" x-model="form.name">

                    <button type="submit">
                        <span x-text="editMode ? 'Update' : 'Save'"></span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Delete Confirm --}}
        <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="showDeleteModal = false"></div>
            <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 text-center"
                @click.stop>
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-trash text-red-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Delete Specialization?</h3>
                <p class="text-gray-500 mb-6">Remove <strong x-text="deleteTarget.name"></strong>?</p>
                <form :action="`/admin/specializations/${deleteTarget.id}`" method="POST">
                    @csrf @method('DELETE')
                    <div class="flex space-x-3">
                        <button type="button" @click="showDeleteModal = false"
                            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-600">Cancel</button>
                        <button type="submit"
                            class="flex-1 py-2.5 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function specManagement() {
            return {
                showModal: false,
                showDeleteModal: false,
                editMode: false,
                deleteTarget: {
                    id: null,
                    name: ''
                },
                form: {
                    id: null,
                    name: '',
                    description: ''
                },
                openModal() {
                    this.editMode = false;
                    this.form = {
                        id: null,
                        name: '',
                        description: ''
                    };
                    this.showModal = true;
                },
                editSpec(s) {
                    this.editMode = true;
                    this.form = {
                        ...s
                    };
                    this.showModal = true;
                },
                confirmDelete(id, name) {
                    this.deleteTarget = {
                        id,
                        name
                    };
                    this.showDeleteModal = true;
                }
                editSpec(s) {
                    console.log(s);
                    this.editMode = true;
                    this.form = {
                        ...s
                    };
                    this.showModal = true;
                }
            }
        }
    </script>
@endpush
