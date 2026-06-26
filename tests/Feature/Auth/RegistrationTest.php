<?php

namespace Tests\Feature\Auth;

use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertDontSee('Преподаватель');
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'student',
        ]);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_ignores_teacher_role_from_request(): void
    {
        $this->post('/register', [
            'name' => 'Fake Teacher',
            'email' => 'fake.teacher@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'teacher',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'fake.teacher@example.com',
            'role' => 'student',
        ]);
    }

    public function test_student_registration_links_existing_student_record_by_email(): void
    {
        $group = Group::create([
            'name' => 'IS-21',
        ]);

        $student = Student::create([
            'first_name' => 'Test',
            'last_name' => 'Student',
            'email' => 'student@example.com',
            'group_id' => $group->id,
        ]);

        $this->post('/register', [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'student@example.com')->firstOrFail();

        $this->assertSame($user->id, $student->fresh()->user_id);
    }
}
