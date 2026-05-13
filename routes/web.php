<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\HospitalController;
use App\Http\Controllers\Public\DoctorController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminHospitalController;
use App\Http\Controllers\Admin\AdminSpecializationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminDoctorController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\Admin\AdminPatientController;
use App\Http\Controllers\Admin\AdminReportsController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\PatientRegistrationController;
use App\Http\Controllers\Staff\QueueController;
use App\Http\Controllers\Staff\AppointmentManagementController;
use App\Http\Controllers\Staff\CashPaymentController;
use App\Http\Controllers\Staff\TransactionController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\PatientAppointmentController;
use App\Http\Controllers\User\MedicalRecordController;
use App\Http\Controllers\User\BillController;
use App\Http\Controllers\User\OnlinePaymentController;
use App\Http\Controllers\User\DoctorScheduleController;
use App\Http\Controllers\User\DoctorAppointmentController;
use App\Http\Controllers\User\PrescriptionController;
use App\Http\Controllers\User\PatientHistoryController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

Route::get('/hospitals', [HospitalController::class, 'index'])->name('hospitals.index');
Route::get('/hospitals/{id}', [HospitalController::class, 'show'])->name('hospitals.show');

Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
Route::get('/doctors/{id}', [DoctorController::class, 'show'])->name('doctors.show');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin,admin_rs'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('hospitals', AdminHospitalController::class)->except(['show']);
    Route::resource('specializations', AdminSpecializationController::class)->except(['show']);
    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::resource('doctors', AdminDoctorController::class)->except(['show']);
    Route::resource('staff', AdminStaffController::class)->except(['show']);
    Route::resource('schedules', AdminScheduleController::class)->except(['show']);
    Route::get('patients', [AdminPatientController::class, 'index'])->name('patients.index');

    Route::get('reports', [AdminReportsController::class, 'index'])->name('reports');
    Route::get('revenue', [AdminReportsController::class, 'revenue'])->name('revenue');
    Route::get('visits', [AdminReportsController::class, 'visits'])->name('visits');
    Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings');
    Route::post('settings', [AdminSettingsController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
*/

Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('registration', [PatientRegistrationController::class, 'index'])->name('registration');
    Route::post('registration', [PatientRegistrationController::class, 'store'])->name('registration.store');
    Route::get('queue', [QueueController::class, 'index'])->name('queue');
    Route::post('queue/{queue}/call', [QueueController::class, 'call'])->name('queue.call');
    Route::post('queue/{queue}/complete', [QueueController::class, 'complete'])->name('queue.complete');
    Route::get('appointments', [AppointmentManagementController::class, 'index'])->name('appointments');
    Route::patch('appointments/{id}', [AppointmentManagementController::class, 'update'])->name('appointments.update');
    Route::get('payment', [CashPaymentController::class, 'index'])->name('payment');
    Route::post('payment', [CashPaymentController::class, 'process'])->name('payment.process');
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions');
});

/*
|--------------------------------------------------------------------------
| User / Doctor / Patient Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::prefix('dashboard')->name('dashboard.')->middleware(['auth', 'role:patient,doctor'])->group(function () {
    Route::get('/', [UserDashboardController::class, 'index'])->name('index');
    Route::get('profile', [UserProfileController::class, 'index'])->name('profile');
    Route::put('profile', [UserProfileController::class, 'update'])->name('profile.update');

    // Patient routes
    Route::get('appointments', [PatientAppointmentController::class, 'index'])->name('appointments');
    Route::post('appointments', [PatientAppointmentController::class, 'store'])->name('appointments.store');
    Route::delete('appointments/{id}', [PatientAppointmentController::class, 'destroy'])->name('appointments.destroy');

    Route::get('medical-records', [MedicalRecordController::class, 'index'])->name('records');
    Route::get('bills', [BillController::class, 'index'])->name('bills');
    Route::get('payment/{bill}', [OnlinePaymentController::class, 'show'])->name('payment');
    Route::post('payment/{bill}', [OnlinePaymentController::class, 'process'])->name('payment.process');

    Route::get('patient-history/{patientId}', [PatientHistoryController::class, 'show'])->name('patient.history');

    // Doctor routes
    Route::get('schedule', [DoctorScheduleController::class, 'index'])->name('schedule');
    Route::get('today', [DoctorAppointmentController::class, 'index'])->name('today');
    Route::get('examination/{appointment}', [MedicalRecordController::class, 'create'])->name('examination');
    Route::post('examination/{appointment}', [MedicalRecordController::class, 'store'])->name('examination.store');
    Route::get('prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions');
});
