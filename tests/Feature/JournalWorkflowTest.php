<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_subject_with_description(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $group = Group::create([
            'name' => 'IS-21',
        ]);

        $this->actingAs($teacher)->post(route('subjects.store'), [
            'name' => 'Математика',
            'group_id' => $group->id,
            'description' => 'Алгебра и анализ',
        ])->assertRedirect(route('subjects.index'));

        $this->assertDatabaseHas('subjects', [
            'name' => 'Математика',
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
            'description' => 'Алгебра и анализ',
        ]);
    }

    public function test_two_teachers_can_teach_same_subject_in_same_group_with_separate_journals(): void
    {
        $firstTeacher = User::factory()->create([
            'name' => 'First Teacher',
            'role' => 'teacher',
        ]);
        $secondTeacher = User::factory()->create([
            'name' => 'Second Teacher',
            'role' => 'teacher',
        ]);
        $group = Group::create(['name' => 'PO-22']);

        $this->actingAs($firstTeacher)
            ->post(route('subjects.store'), [
                'name' => 'Mathematics',
                'description' => 'First journal',
                'group_id' => $group->id,
            ])
            ->assertRedirect(route('subjects.index'));

        $this->actingAs($secondTeacher)
            ->post(route('subjects.store'), [
                'name' => 'Mathematics',
                'description' => 'Second journal',
                'group_id' => $group->id,
            ])
            ->assertRedirect(route('subjects.index'));

        $this->assertDatabaseCount('subjects', 2);
        $this->assertDatabaseHas('subjects', [
            'teacher_id' => $firstTeacher->id,
            'group_id' => $group->id,
            'name' => 'Mathematics',
        ]);
        $this->assertDatabaseHas('subjects', [
            'teacher_id' => $secondTeacher->id,
            'group_id' => $group->id,
            'name' => 'Mathematics',
        ]);

        $this->actingAs($secondTeacher)
            ->get(route('subjects.index'))
            ->assertOk()
            ->assertSee('First Teacher');
    }

    public function test_teacher_cannot_duplicate_own_subject_in_same_group(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $group = Group::create(['name' => 'PO-22']);

        Subject::create([
            'teacher_id' => $teacher->id,
            'group_id' => $group->id,
            'name' => 'Mathematics',
        ]);

        $this->actingAs($teacher)
            ->post(route('subjects.store'), [
                'name' => '  mathematics  ',
                'description' => 'Duplicate assignment',
                'group_id' => $group->id,
            ])
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('subjects', 1);
    }

    public function test_teacher_resource_pages_render(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $group = Group::create([
            'name' => 'IS-21',
            'description' => 'Вторая группа',
        ]);

        $subject = Subject::create([
            'name' => 'Математика',
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
            'description' => 'Алгебра',
        ]);

        $this->actingAs($teacher)->get(route('groups.show', $group))->assertOk();
        $this->actingAs($teacher)->get(route('groups.edit', $group))->assertOk();
        $this->actingAs($teacher)->get(route('subjects.show', $subject))->assertOk();
    }

    public function test_group_short_name_is_generated_from_program_course_and_number(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($teacher)
            ->post(route('groups.store'), [
                'program_name' => 'Вычислительные технологии',
                'course' => 2,
                'group_number' => 2,
            ])
            ->assertRedirect(route('groups.index'));

        $this->assertDatabaseHas('groups', [
            'name' => 'ВТ-22',
            'description' => 'Вычислительные технологии, 2 курс, 2 группа',
        ]);
    }

    public function test_group_short_name_updates_with_same_template(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $group = Group::create([
            'name' => 'OLD-11',
            'description' => 'Старое название',
        ]);

        $this->actingAs($teacher)
            ->patch(route('groups.update', $group), [
                'program_name' => 'Программное обеспечение',
                'course' => 4,
                'group_number' => 1,
            ])
            ->assertRedirect(route('groups.index'));

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'ПО-41',
            'description' => 'Программное обеспечение, 4 курс, 1 группа',
        ]);
    }

    public function test_groups_index_defaults_to_highest_course_first(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        Group::create([
            'name' => 'ПО-12',
            'description' => 'Программное обеспечение, 1 курс, 2 группа',
        ]);
        Group::create([
            'name' => 'ВТ-22',
            'description' => 'Вычислительные технологии, 2 курс, 2 группа',
        ]);
        Group::create([
            'name' => 'ПО-41',
            'description' => 'Программное обеспечение, 4 курс, 1 группа',
        ]);

        $this->actingAs($teacher)
            ->get(route('groups.index'))
            ->assertOk()
            ->assertSeeInOrder(['ПО-41', 'ВТ-22', 'ПО-12']);
    }

    public function test_groups_index_keeps_sorting_options_simple(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        Group::create([
            'name' => 'ПО-12',
            'description' => 'Программное обеспечение, 1 курс, 2 группа',
        ]);

        $this->actingAs($teacher)
            ->get(route('groups.index'))
            ->assertOk()
            ->assertSee('Старшие курсы сверху')
            ->assertSee('Младшие курсы сверху')
            ->assertDontSee('Больше студентов')
            ->assertDontSee('Больше предметов')
            ->assertDontSee('Больше уроков');
    }

    public function test_demo_seed_creates_six_groups_with_three_subjects_for_one_teacher(): void
    {
        $this->seed(DemoSeeder::class);

        $teacher = User::where('email', 'teacher@journal.local')->first();

        $this->assertNotNull($teacher);
        $this->assertSame(1, User::where('role', 'teacher')->count());
        $this->assertSame(6, Group::count());
        $this->assertSame(18, Subject::where('teacher_id', $teacher->id)->count());

        Group::withCount('subjects')->get()->each(function (Group $group): void {
            $this->assertSame(3, $group->subjects_count, $group->name);
        });
    }

    public function test_public_landing_install_and_contact_pages_render(): void
    {
        $this->get(route('welcome'))
            ->assertOk()
            ->assertSee('Возможности')
            ->assertSee('Приложения')
            ->assertSee('Как работает Journal')
            ->assertSee('Контакты');

        $this->get(route('download.windows'))->assertOk()->assertSee('Установить Journal на Windows');
        $this->get(route('download.windows.installer'))->assertOk();
        $this->get(route('download.android'))->assertOk()->assertSee('Установить Journal на Android');
        $this->get(route('contacts'))->assertOk()->assertSee('Контакты');
    }

    public function test_admin_can_open_students_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('students.index'))
            ->assertOk()
            ->assertSee('Студенты');
    }

    public function test_absent_attendance_is_saved_as_attendance_only_record(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $group = Group::create(['name' => 'ПО-22']);
        $student = Student::create([
            'first_name' => 'Иван',
            'last_name' => 'Петров',
            'student_number' => 'S-100',
            'email' => 'student100@example.com',
            'group_id' => $group->id,
            'login_password' => 'student123',
        ]);

        $subject = Subject::create([
            'name' => 'Информатика',
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
        ]);

        $lesson = Lesson::create([
            'group_id' => $group->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-02',
            'topic' => 'Практическая работа',
        ]);

        $this->actingAs($teacher)->post(route('attendance.lesson.save', $lesson), [
            'attendance' => [
                $student->id => ['status' => 'absent', 'note' => 'Медсправка'],
            ],
        ])->assertRedirect(route('groups.attendance', [
            'group' => $group->id,
            'subject_id' => $subject->id,
        ]));

        $this->assertDatabaseHas('attendances', [
            'lesson_id' => $lesson->id,
            'student_id' => $student->id,
            'status' => 'absent',
            'note' => 'Медсправка',
        ]);
        $this->actingAs($teacher)
            ->from(route('attendance.lesson.mark', $lesson))
            ->post(route('attendance.lesson.save', $lesson), [
                'attendance' => [
                    $student->id => ['status' => ''],
                ],
            ])
            ->assertSessionHasErrors("attendance.{$student->id}.status");

        $this->assertDatabaseHas('attendances', [
            'lesson_id' => $lesson->id,
            'student_id' => $student->id,
            'status' => 'absent',
        ]);
    }

    public function test_only_present_or_absent_attendance_statuses_are_allowed(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $group = Group::create(['name' => 'PO-22']);

        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'student_number' => 'S-101',
            'email' => 'student101@example.com',
            'group_id' => $group->id,
            'login_password' => 'student123',
        ]);

        $subject = Subject::create([
            'name' => 'Algorithms',
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
        ]);

        $lesson = Lesson::create([
            'group_id' => $group->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-12',
            'topic' => 'Validation',
        ]);

        $this->actingAs($teacher)
            ->from(route('attendance.lesson.mark', $lesson))
            ->post(route('attendance.lesson.save', $lesson), [
                'attendance' => [
                    $student->id => ['status' => 'excused'],
                ],
            ])
            ->assertSessionHasErrors("attendance.{$student->id}.status");

        $this->assertDatabaseMissing('attendances', [
            'lesson_id' => $lesson->id,
            'student_id' => $student->id,
            'status' => 'excused',
        ]);
    }

    public function test_lesson_creation_is_available_only_inside_selected_subject(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $group = Group::create(['name' => 'VT-23']);

        $subject = Subject::create([
            'name' => 'Networks',
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('groups.attendance', $group))
            ->assertOk()
            ->assertDontSee('name="return_to"', false)
            ->assertDontSee('name="description"', false)
            ->assertDontSee('not_filled', false);

        $this->actingAs($teacher)
            ->get(route('groups.attendance', [
                'group' => $group->id,
                'subject_id' => $subject->id,
            ]))
            ->assertOk()
            ->assertSee('name="return_to"', false)
            ->assertSee('name="description"', false);
    }

    public function test_attendance_mark_form_uses_only_present_or_absent_choices(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $group = Group::create(['name' => 'PO-22']);
        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'student_number' => 'S-201',
            'email' => 'student201@example.com',
            'group_id' => $group->id,
            'login_password' => 'student123',
        ]);

        $subject = Subject::create([
            'name' => 'Algorithms',
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
        ]);

        $lesson = Lesson::create([
            'group_id' => $group->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-12',
            'topic' => 'Validation',
        ]);

        $this->actingAs($teacher)
            ->get(route('attendance.lesson.mark', $lesson))
            ->assertOk()
            ->assertSee('Присутствовал')
            ->assertSee('Отсутствовал')
            ->assertDontSee('Не отмечено')
            ->assertDontSee('Снять отметки');
    }

    public function test_deleting_group_removes_its_learning_data_and_student_accounts(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $studentUser = User::factory()->create([
            'role' => 'student',
            'email' => 'student-delete@example.com',
            'login_password' => 'student123',
        ]);

        $group = Group::create(['name' => 'PO-41']);
        $student = Student::create([
            'first_name' => 'Delete',
            'last_name' => 'Student',
            'student_number' => 'S-301',
            'email' => 'student-delete@example.com',
            'group_id' => $group->id,
            'user_id' => $studentUser->id,
            'login_password' => 'student123',
        ]);
        $subject = Subject::create([
            'name' => 'Programming',
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
        ]);
        $lesson = Lesson::create([
            'group_id' => $group->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-12',
            'topic' => 'Loops',
        ]);
        $attendance = Attendance::create([
            'lesson_id' => $lesson->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-12',
            'status' => 'present',
        ]);

        $this->actingAs($teacher)
            ->delete(route('groups.destroy', $group))
            ->assertRedirect(route('groups.index'));

        $this->assertDatabaseMissing('groups', ['id' => $group->id]);
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
        $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);
        $this->assertDatabaseMissing('users', ['id' => $studentUser->id]);
    }

    public function test_student_edit_page_shows_current_login_password(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $group = Group::create(['name' => 'PO-22']);
        $student = Student::create([
            'first_name' => 'Password',
            'last_name' => 'Student',
            'student_number' => 'S-401',
            'email' => 'student401@example.com',
            'group_id' => $group->id,
            'login_password' => 'student123',
        ]);

        $this->actingAs($teacher)
            ->get(route('students.edit', $student))
            ->assertOk()
            ->assertSee('current_login_password', false)
            ->assertSee('value="student123"', false);
    }

    public function test_student_card_shows_attendance_summary_by_subject(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $group = Group::create(['name' => 'VT-23']);

        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'student_number' => 'S-500',
            'email' => 'student500@example.com',
            'group_id' => $group->id,
            'login_password' => 'student123',
        ]);

        $subject = Subject::create([
            'name' => 'Networks',
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
        ]);

        $lesson = Lesson::create([
            'group_id' => $group->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-03',
            'topic' => 'Routing',
        ]);

        $secondLesson = Lesson::create([
            'group_id' => $group->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-04',
            'topic' => 'Switching',
        ]);

        Attendance::create([
            'lesson_id' => $secondLesson->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-03',
            'status' => 'present',
        ]);

        Attendance::create([
            'lesson_id' => $lesson->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-04',
            'status' => 'absent',
        ]);

        $this->actingAs($teacher)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('data-open-attendance-dialog', false)
            ->assertSee('subjectAttendance'.$subject->id, false)
            ->assertSee('03.06.2026')
            ->assertSee('04.06.2026')
            ->assertSee('Routing')
            ->assertSee('Switching')
            ->assertSee('Посещаемость по предметам')
            ->assertSee('Networks')
            ->assertSee('Всего уроков')
            ->assertSee('Присутствовал')
            ->assertSee('Отсутствовал')
            ->assertSee('Последние 20 отметок посещаемости');
    }

    public function test_updating_student_redirects_to_updated_student_profile(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $group = Group::create(['name' => 'PO-22']);

        $student = Student::create([
            'first_name' => 'Old',
            'last_name' => 'Name',
            'student_number' => 'S-501',
            'email' => 'student501@example.com',
            'group_id' => $group->id,
            'login_password' => 'student123',
        ]);

        $returnTo = route('students.index', ['group_id' => $group->id], false);

        $this->actingAs($teacher)
            ->patch(route('students.update', $student), [
                'first_name' => 'New',
                'last_name' => 'Name',
                'student_number' => 'S-501',
                'email' => 'student501@example.com',
                'group_id' => $group->id,
                'return_to' => $returnTo,
            ])
            ->assertRedirect(route('students.show', [
                'student' => $student->id,
                'return_to' => $returnTo,
            ]));

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'first_name' => 'New',
        ]);
    }

    public function test_student_subject_page_has_summary_and_filters(): void
    {
        $teacher = User::factory()->create([
            'role' => 'teacher',
        ]);

        $studentUser = User::factory()->create([
            'role' => 'student',
            'email' => 'student-subject@example.com',
        ]);

        $group = Group::create(['name' => 'ВТ-23']);

        $student = Student::create([
            'first_name' => 'Олег',
            'last_name' => 'Ахметова',
            'student_number' => 'S-777',
            'email' => 'student-subject@example.com',
            'group_id' => $group->id,
            'user_id' => $studentUser->id,
            'login_password' => 'student123',
        ]);

        $subject = Subject::create([
            'name' => 'Операционные системы',
            'group_id' => $group->id,
            'teacher_id' => $teacher->id,
            'description' => 'Файлы, процессы и права доступа',
        ]);

        $presentLesson = Lesson::create([
            'group_id' => $group->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-01',
            'topic' => 'Файловые системы',
        ]);

        $absentLesson = Lesson::create([
            'group_id' => $group->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-02',
            'topic' => 'Практика по процессам',
        ]);

        Attendance::create([
            'lesson_id' => $presentLesson->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-01',
            'status' => 'present',
        ]);

        Attendance::create([
            'lesson_id' => $absentLesson->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'date' => '2026-06-02',
            'status' => 'absent',
            'note' => 'Был у врача',
        ]);

        $this->actingAs($studentUser)
            ->get(route('student.attendance.history'))
            ->assertOk()
            ->assertSee('Операционные системы')
            ->assertSee('Посещаемость')
            ->assertSee('50%');

        $this->actingAs($studentUser)
            ->get(route('student.attendance.subject', $subject))
            ->assertOk()
            ->assertSee('Всего уроков')
            ->assertSee('Посещаемость')
            ->assertSee('Файловые системы')
            ->assertSee('Практика по процессам')
            ->assertSee('Был у врача');

        $this->actingAs($studentUser)
            ->get(route('student.attendance.subject', [
                'subject' => $subject->id,
                'status' => 'absent',
            ]))
            ->assertOk()
            ->assertSee('Практика по процессам')
            ->assertSee('Отсутствовал')
            ->assertDontSee('Файловые системы');
    }
}
