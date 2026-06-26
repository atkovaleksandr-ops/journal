<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTeacherTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_can_be_rendered(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Личный кабинет администратора');
    }

    public function test_admin_can_create_teacher(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.teachers.store'), [
            'name' => 'Teacher One',
            'email' => 'teacher.one@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'teacher.one@example.com',
            'role' => 'teacher',
        ]);

        $teacher = User::where('email', 'teacher.one@example.com')->firstOrFail();
        $this->assertSame('password', $teacher->login_password);
    }

    public function test_teacher_cannot_access_admin_teacher_pages(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->get(route('admin.teachers.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_open_and_update_teacher(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'name' => 'Old Teacher',
            'email' => 'old.teacher@example.com',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.teachers.show', $teacher))
            ->assertOk()
            ->assertSee('Old Teacher');

        $this->actingAs($admin)
            ->patch(route('admin.teachers.update', $teacher), [
                'name' => 'Updated Teacher',
                'email' => 'updated.teacher@example.com',
            ])
            ->assertRedirect(route('admin.teachers.show', $teacher));

        $this->assertDatabaseHas('users', [
            'id' => $teacher->id,
            'name' => 'Updated Teacher',
            'email' => 'updated.teacher@example.com',
            'role' => 'teacher',
        ]);
    }

    public function test_admin_can_reset_teacher_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);

        $this->actingAs($admin)
            ->patch(route('admin.teachers.password', $teacher), [
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect(route('admin.teachers.edit', $teacher));

        $teacher->refresh();

        $this->assertTrue(Hash::check('new-secure-password', $teacher->password));
        $this->assertSame('new-secure-password', $teacher->login_password);
    }

    public function test_admin_security_page_can_be_rendered(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.security'))
            ->assertOk()
            ->assertSee('Доступы')
            ->assertSee('Студенты без данных для входа')
            ->assertSee('Преподаватели без предметов')
            ->assertDontSee('Порядок доступа');
    }

    public function test_download_version_endpoint_returns_package_metadata(): void
    {
        $response = $this->get(route('download.version'));

        $response->assertOk()
            ->assertJsonPath('name', 'Journal')
            ->assertJsonStructure([
                'version',
                'windows' => ['available', 'installer_url', 'package_url'],
                'android' => ['available', 'apk_url'],
            ]);
    }

    public function test_student_email_cannot_reuse_teacher_email(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create([
            'role' => 'teacher',
            'email' => 'busy.teacher@example.com',
        ]);
        $group = Group::create([
            'name' => 'ИС-21',
            'description' => 'Тестовая группа',
        ]);

        $this->actingAs($admin)
            ->post(route('students.store'), [
                'first_name' => 'Тест',
                'last_name' => 'Студент',
                'student_number' => 'S-777',
                'email' => $teacher->email,
                'login_password' => 'student123',
                'group_id' => $group->id,
            ])
            ->assertSessionHasErrors('email');
    }
}
