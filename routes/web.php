<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubjectController;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\AttendanceSummaryService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Главный кабинет после входа
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return view('admin.dashboard', [
            'teachersCount' => User::where('role', 'teacher')->count(),
            'studentsCount' => Student::count(),
            'groupsCount' => Group::count(),
            'subjectsCount' => Subject::count(),
            'lessonsCount' => Lesson::count(),
            'attendancesCount' => Attendance::count(),
            'studentsWithoutLoginCount' => Student::whereNull('user_id')
                ->orWhereNull('email')
                ->count(),
            'teachersWithoutSubjectsCount' => User::where('role', 'teacher')
                ->doesntHave('subjects')
                ->count(),
            'latestTeachers' => User::where('role', 'teacher')
                ->latest()
                ->limit(4)
                ->get(),
            'latestStudents' => Student::with('group')
                ->latest()
                ->limit(4)
                ->get(),
        ]);
    }

    if (auth()->user()->role === 'teacher') {
        return view('teacher.dashboard', [
            'groupsCount' => Group::count(),
            'studentsCount' => Student::count(),
            'subjectsCount' => Subject::where('teacher_id', auth()->id())->count(),
            'lessonsCount' => Lesson::where('teacher_id', auth()->id())->count(),
        ]);
    }

    if (auth()->user()->role === 'student') {
        $student = auth()->user()->student;
        $attendanceSummary = $student
            ? app(AttendanceSummaryService::class)->forStudent($student)['summary']
            : [
                'lessons' => 0,
                'present' => 0,
                'absent' => 0,
                'attendance_percent' => null,
            ];

        return view('student.dashboard', [
            'student' => $student,
            'subjectsCount' => $student ? Subject::where('group_id', $student->group_id)->count() : 0,
            'lessonsCount' => $attendanceSummary['lessons'],
            'presentCount' => $attendanceSummary['present'],
            'absentCount' => $attendanceSummary['absent'],
            'attendancePercent' => $attendanceSummary['attendance_percent'],
        ]);
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Профиль пользователя
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Маршруты для преподавателя
Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    Route::resource('students', StudentController::class);
});

Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::resource('groups', GroupController::class);
    Route::resource('subjects', SubjectController::class);

    Route::get('/groups/{group}/attendance', [AttendanceController::class, 'show'])
        ->name('groups.attendance');

    Route::post('/attendance/lessons', [AttendanceController::class, 'storeLesson'])
        ->name('attendance.lessons.store');

    Route::get('/attendance/lessons/{lesson}/mark', [AttendanceController::class, 'markForm'])
        ->name('attendance.lesson.mark');

    Route::post('/attendance/lessons/{lesson}/mark', [AttendanceController::class, 'mark'])
        ->name('attendance.lesson.save');
    Route::delete('/attendance/lessons/{lesson}', [AttendanceController::class, 'destroyLesson'])
        ->name('attendance.lesson.destroy');    
});

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/security', function () {
            return view('admin.security', [
                'studentsWithoutLogin' => Student::with('group')
                    ->whereNull('user_id')
                    ->orWhereNull('email')
                    ->latest()
                    ->get(),
                'teachersWithoutSubjects' => User::where('role', 'teacher')
                    ->doesntHave('subjects')
                    ->latest()
                    ->get(),
                'adminsCount' => User::where('role', 'admin')->count(),
                'teachersCount' => User::where('role', 'teacher')->count(),
                'studentsCount' => Student::count(),
            ]);
        })->name('security');

        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');
        Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::patch('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::patch('/teachers/{teacher}/password', [TeacherController::class, 'resetPassword'])->name('teachers.password');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    });

// Маршруты для студента
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/my-attendance', [AttendanceController::class, 'history'])
        ->name('student.attendance.history');

    Route::get('/my-attendance/subjects/{subject}', [AttendanceController::class, 'historySubject'])
        ->name('student.attendance.subject');
});

Route::get('/download/windows/file', function () {
    $path = public_path('downloads/Journal-Windows-Setup.exe');

    abort_unless(file_exists($path), 404);

    return response()->download($path, 'Journal-Windows-Setup.exe');
})->name('download.windows.file');

Route::get('/download/windows/installer', function () {
    $path = public_path('downloads/Journal-Windows-Setup.exe');

    abort_unless(file_exists($path), 404);

    return response()->download($path, 'Journal-Windows-Setup.exe');
})->name('download.windows.installer');

Route::get('/download/version', function (Illuminate\Http\Request $request) {
    $windowsPath = public_path('downloads/Journal-Windows-Setup.exe');
    $androidPath = public_path('downloads/Journal-Android.apk');
    $packagePath = base_path('apps/windows-electron/package.json');
    $version = '1.0.0';
    $baseUrl = $request->getSchemeAndHttpHost();

    if (file_exists($packagePath)) {
        $package = json_decode((string) file_get_contents($packagePath), true);
        $version = $package['version'] ?? $version;
    }

    return response()->json([
        'name' => 'Journal',
        'version' => $version,
        'site_url' => $request->getSchemeAndHttpHost(),
        'generated_at' => now()->toIso8601String(),
        'windows' => [
            'available' => file_exists($windowsPath),
            'size_mb' => file_exists($windowsPath) ? round(filesize($windowsPath) / 1024 / 1024, 1) : null,
            'updated_at' => file_exists($windowsPath) ? date(DATE_ATOM, filemtime($windowsPath)) : null,
            'installer_url' => $baseUrl.route('download.windows.installer', [], false),
            'package_url' => $baseUrl.route('download.windows.file', [], false),
        ],
        'android' => [
            'available' => file_exists($androidPath),
            'size_mb' => file_exists($androidPath) ? round(filesize($androidPath) / 1024 / 1024, 1) : null,
            'updated_at' => file_exists($androidPath) ? date(DATE_ATOM, filemtime($androidPath)) : null,
            'apk_url' => $baseUrl.route('download.android.file', [], false),
        ],
    ])->header('Cache-Control', 'no-store');
})->name('download.version');

Route::get('/download/android/file', function () {
    $path = public_path('downloads/Journal-Android.apk');

    abort_unless(file_exists($path), 404);

    return response()->download($path, 'Journal-Android.apk', [
        'Content-Type' => 'application/vnd.android.package-archive',
    ]);
})->name('download.android.file');

// Страницы приложений
Route::get('/download/windows', function () {
    return view('download.windows');
})->name('download.windows');

Route::get('/download/android', function () {
    return view('download.android');
})->name('download.android');

Route::get('/contacts', function () {
    return view('contacts');
})->name('contacts');

require __DIR__.'/auth.php';
