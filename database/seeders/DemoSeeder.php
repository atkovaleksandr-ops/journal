<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => env('DEMO_ADMIN_EMAIL', 'admin@journal.demo')],
            [
                'name' => env('DEMO_ADMIN_NAME', 'Administrator'),
                'password' => Hash::make(env('DEMO_ADMIN_PASSWORD', 'admin12345')),
                'role' => 'admin',
            ],
        );

        $teacher = User::updateOrCreate(
            ['email' => env('DEMO_TEACHER_EMAIL', 'teacher@journal.demo')],
            [
                'name' => env('DEMO_TEACHER_NAME', 'Teacher Demo'),
                'password' => Hash::make(env('DEMO_TEACHER_PASSWORD', 'teacher12345')),
                'login_password' => env('DEMO_TEACHER_PASSWORD', 'teacher12345'),
                'role' => 'teacher',
            ],
        );

        $studentUser = User::updateOrCreate(
            ['email' => env('DEMO_STUDENT_EMAIL', 'student@journal.demo')],
            [
                'name' => env('DEMO_STUDENT_NAME', 'Student Demo'),
                'password' => Hash::make(env('DEMO_STUDENT_PASSWORD', 'student12345')),
                'role' => 'student',
            ],
        );

        $group = Group::updateOrCreate(
            ['name' => 'DEMO-21'],
            ['description' => 'Demo group for public preview'],
        );

        $student = Student::updateOrCreate(
            ['email' => $studentUser->email],
            [
                'first_name' => 'Student',
                'last_name' => 'Demo',
                'student_number' => 'D-001',
                'login_password' => env('DEMO_STUDENT_PASSWORD', 'student12345'),
                'group_id' => $group->id,
                'user_id' => $studentUser->id,
            ],
        );

        $secondStudent = Student::updateOrCreate(
            ['student_number' => 'D-002'],
            [
                'first_name' => 'Second',
                'last_name' => 'Student',
                'email' => null,
                'login_password' => null,
                'group_id' => $group->id,
                'user_id' => null,
            ],
        );

        $subjects = collect([
            ['name' => 'Mathematics', 'description' => 'Functions, equations and practice tasks'],
            ['name' => 'Web Development', 'description' => 'Laravel, routes, templates and student accounts'],
            ['name' => 'Databases', 'description' => 'Tables, relations, queries and attendance data'],
        ])->map(fn (array $data) => Subject::updateOrCreate(
            [
                'name' => $data['name'],
                'group_id' => $group->id,
                'teacher_id' => $teacher->id,
            ],
            ['description' => $data['description']],
        ));

        $baseDate = Carbon::today()->subDays(14);

        $subjects->each(function (Subject $subject, int $subjectIndex) use ($baseDate, $group, $teacher, $student, $secondStudent) {
            for ($i = 0; $i < 4; $i++) {
                $lesson = Lesson::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'group_id' => $group->id,
                        'teacher_id' => $teacher->id,
                        'date' => $baseDate->copy()->addDays($subjectIndex * 2 + $i * 3)->toDateString(),
                        'topic' => 'Demo lesson ' . ($i + 1),
                    ],
                    ['description' => 'Prepared lesson for public preview'],
                );

                $status = $i === 2 ? Attendance::STATUS_ABSENT : Attendance::STATUS_PRESENT;

                Attendance::updateOrCreate(
                    ['lesson_id' => $lesson->id, 'student_id' => $student->id],
                    [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'date' => $lesson->date,
                        'status' => $status,
                        'note' => $status === Attendance::STATUS_ABSENT ? 'Demo absence note' : null,
                    ],
                );

                Attendance::updateOrCreate(
                    ['lesson_id' => $lesson->id, 'student_id' => $secondStudent->id],
                    [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'date' => $lesson->date,
                        'status' => Attendance::STATUS_PRESENT,
                        'note' => null,
                    ],
                );
            }
        });

        $admin->touch();
    }
}
