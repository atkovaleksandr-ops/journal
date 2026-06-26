<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_without_server_errors(): void
    {
        foreach ($this->publicUrls() as $url) {
            $this->get($url)->assertStatus(200);
        }
    }

    public function test_admin_pages_render_without_server_errors(): void
    {
        $fixture = $this->fixture();

        foreach ($this->adminUrls($fixture) as $url) {
            $this->actingAs($fixture['admin'])->get($url)->assertStatus(200);
        }
    }

    public function test_teacher_pages_render_without_server_errors(): void
    {
        $fixture = $this->fixture();

        foreach ($this->teacherUrls($fixture) as $url) {
            $this->actingAs($fixture['teacher'])->get($url)->assertStatus(200);
        }
    }

    public function test_student_pages_render_without_server_errors(): void
    {
        $fixture = $this->fixture();

        foreach ($this->studentUrls($fixture) as $url) {
            $this->actingAs($fixture['studentUser'])->get($url)->assertStatus(200);
        }
    }

    public function test_package_download_routes_do_not_throw_server_errors(): void
    {
        foreach ([
            route('download.windows.file'),
            route('download.windows.installer'),
            route('download.android.file'),
        ] as $url) {
            $this->assertContains($this->get($url)->getStatusCode(), [200, 404]);
        }
    }

    public function test_static_route_helpers_point_to_registered_routes(): void
    {
        $missing = [];
        $roots = [resource_path('views'), app_path(), base_path('routes')];

        foreach ($roots as $root) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));

            foreach ($iterator as $file) {
                if (!$file->isFile()) {
                    continue;
                }

                $path = $file->getPathname();

                if (!preg_match('/\.(php|blade\.php)$/', $path)) {
                    continue;
                }

                $content = file_get_contents($path);

                preg_match_all('/(?<!->)\broute\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);

                foreach (array_unique($matches[1]) as $name) {
                    if (!\Illuminate\Support\Facades\Route::has($name)) {
                        $missing[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path) . ' => ' . $name;
                    }
                }
            }
        }

        $this->assertSame([], $missing);
    }

    /**
     * @return array<string, mixed>
     */
    private function fixture(): array
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Teacher One']);
        $otherTeacher = User::factory()->create(['role' => 'teacher', 'name' => 'Teacher Two']);
        $studentUser = User::factory()->create(['role' => 'student', 'name' => 'Student One']);

        $group = Group::create([
            'name' => 'IS-21',
            'description' => 'Smoke group',
        ]);

        $student = Student::create([
            'first_name' => 'Student',
            'last_name' => 'One',
            'student_number' => 'S-001',
            'email' => $studentUser->email,
            'login_password' => 'student123',
            'group_id' => $group->id,
            'user_id' => $studentUser->id,
        ]);

        Student::create([
            'first_name' => 'Student',
            'last_name' => 'Two',
            'student_number' => 'S-002',
            'email' => null,
            'login_password' => null,
            'group_id' => $group->id,
        ]);

        $subject = Subject::create([
            'name' => 'Mathematics',
            'description' => 'Smoke subject',
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
        ]);

        Subject::create([
            'name' => 'Mathematics',
            'description' => 'Co-teacher subject',
            'group_id' => $group->id,
            'teacher_id' => $otherTeacher->id,
        ]);

        $lesson = Lesson::create([
            'subject_id' => $subject->id,
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
            'date' => now()->toDateString(),
            'topic' => 'Smoke lesson',
            'description' => 'Rendered by route smoke test',
        ]);

        Attendance::create([
            'lesson_id' => $lesson->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => $lesson->date,
            'status' => Attendance::STATUS_ABSENT,
            'note' => 'Smoke note',
        ]);

        return compact('admin', 'teacher', 'otherTeacher', 'studentUser', 'group', 'student', 'subject', 'lesson');
    }

    /**
     * @return array<int, string>
     */
    private function publicUrls(): array
    {
        return [
            route('welcome'),
            route('login'),
            route('register'),
            route('password.request'),
            route('password.reset', ['token' => 'smoke-token']),
            route('contacts'),
            route('download.windows'),
            route('download.android'),
            route('download.version'),
        ];
    }

    /**
     * @param array<string, mixed> $fixture
     * @return array<int, string>
     */
    private function adminUrls(array $fixture): array
    {
        return [
            route('dashboard'),
            route('profile.edit'),
            route('admin.security'),
            route('admin.teachers.index'),
            route('admin.teachers.create'),
            route('admin.teachers.show', $fixture['teacher']),
            route('admin.teachers.edit', $fixture['teacher']),
            route('students.index'),
            route('students.create'),
            route('students.show', $fixture['student']),
            route('students.edit', $fixture['student']),
        ];
    }

    /**
     * @param array<string, mixed> $fixture
     * @return array<int, string>
     */
    private function teacherUrls(array $fixture): array
    {
        return [
            route('dashboard'),
            route('profile.edit'),
            route('groups.index'),
            route('groups.create'),
            route('groups.show', $fixture['group']),
            route('groups.edit', $fixture['group']),
            route('groups.attendance', $fixture['group']),
            route('groups.attendance', ['group' => $fixture['group'], 'subject_id' => $fixture['subject']->id]),
            route('groups.attendance', [
                'group' => $fixture['group'],
                'subject_id' => $fixture['subject']->id,
                'status' => 'has_absent',
                'sort' => 'fill_desc',
            ]),
            route('attendance.lesson.mark', $fixture['lesson']),
            route('students.index'),
            route('students.create'),
            route('students.show', $fixture['student']),
            route('students.edit', $fixture['student']),
            route('subjects.index'),
            route('subjects.create'),
            route('subjects.show', $fixture['subject']),
            route('subjects.edit', $fixture['subject']),
        ];
    }

    /**
     * @param array<string, mixed> $fixture
     * @return array<int, string>
     */
    private function studentUrls(array $fixture): array
    {
        return [
            route('dashboard'),
            route('profile.edit'),
            route('student.attendance.history'),
            route('student.attendance.subject', $fixture['subject']),
            route('student.attendance.subject', [
                'subject' => $fixture['subject'],
                'status' => Attendance::STATUS_ABSENT,
                'sort' => 'date_asc',
            ]),
        ];
    }
}
