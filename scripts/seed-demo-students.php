<?php

use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$names = [
    ['Алексей', 'Громов'],
    ['Мария', 'Соколова'],
    ['Илья', 'Кузнецов'],
    ['Алина', 'Волкова'],
    ['Данияр', 'Ахметов'],
    ['Екатерина', 'Морозова'],
    ['Никита', 'Попов'],
    ['София', 'Лебедева'],
    ['Артур', 'Нурланов'],
    ['Виктория', 'Орлова'],
    ['Кирилл', 'Смирнов'],
    ['Анна', 'Федорова'],
    ['Тимур', 'Ибраев'],
    ['Полина', 'Егорова'],
    ['Дмитрий', 'Павлов'],
    ['Дарья', 'Новикова'],
    ['Роман', 'Михайлов'],
    ['Ева', 'Ким'],
    ['Максим', 'Тихонов'],
    ['Камила', 'Серикова'],
    ['Руслан', 'Беляев'],
    ['Вероника', 'Зайцева'],
    ['Станислав', 'Ковалев'],
    ['Айша', 'Омарова'],
    ['Глеб', 'Васильев'],
    ['Милана', 'Петрова'],
    ['Ерлан', 'Садыков'],
    ['Ксения', 'Борисова'],
    ['Матвей', 'Андреев'],
    ['Аружан', 'Касымова'],
];

$groups = Group::query()->orderBy('id')->get();

if ($groups->isEmpty()) {
    $groups = collect([
        Group::create(['name' => 'ИС-21', 'description' => 'Демо-группа']),
    ]);
}

foreach ($names as $index => [$firstName, $lastName]) {
    $group = $groups[$index % $groups->count()];
    $number = 'S-'.str_pad((string) ($index + 10), 3, '0', STR_PAD_LEFT);
    $email = 'student'.($index + 10).'@journal.local';

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => $firstName.' '.$lastName,
            'password' => Hash::make('password'),
            'role' => 'student',
        ],
    );

    Student::updateOrCreate(
        ['email' => $email],
        [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'student_number' => $number,
            'group_id' => $group->id,
            'user_id' => $user->id,
        ],
    );
}

echo 'Students total: '.Student::count().PHP_EOL;
