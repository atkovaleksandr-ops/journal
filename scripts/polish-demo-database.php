<?php

use App\Models\Attendance;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$hasLatin = static fn (?string $value): bool => $value !== null && preg_match('/[A-Za-z]/', $value) === 1;

$teacher = User::updateOrCreate(
    ['email' => 'teacher@journal.local'],
    [
        'name' => 'РРІР°РЅ РџРµС‚СЂРѕРІРёС‡',
        'password' => Hash::make('password'),
        'role' => 'teacher',
    ]
);

User::updateOrCreate(
    ['email' => 'admin@journal.local'],
    [
        'name' => 'РђРґРјРёРЅРёСЃС‚СЂР°С‚РѕСЂ',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]
);

$groupsData = [
    'РРЎ-21' => 'РРЅС„РѕСЂРјР°С†РёРѕРЅРЅС‹Рµ СЃРёСЃС‚РµРјС‹, 2 РєСѓСЂСЃ',
    'РџРћ-22' => 'РџСЂРѕРіСЂР°РјРјРЅРѕРµ РѕР±РµСЃРїРµС‡РµРЅРёРµ, 1 РєСѓСЂСЃ',
    'Р’Рў-23' => 'Р’С‹С‡РёСЃР»РёС‚РµР»СЊРЅР°СЏ С‚РµС…РЅРёРєР°, 1 РєСѓСЂСЃ',
    'Р”Р-21' => 'Р”РёР·Р°Р№РЅ РёРЅС‚РµСЂС„РµР№СЃРѕРІ, 2 РєСѓСЂСЃ',
];

$groups = [];
foreach ($groupsData as $name => $description) {
    $groups[$name] = Group::updateOrCreate(
        ['name' => $name],
        ['description' => $description]
    );
}

$mainGroup = $groups['РРЎ-21'];

DB::transaction(function () use ($groups, $mainGroup, $teacher) {
    $englishGroups = Group::all()->filter(fn (Group $group) => preg_match('/[A-Za-z]/', $group->name) === 1);

    foreach ($englishGroups as $group) {
    Student::where('group_id', $group->id)->update(['group_id' => $mainGroup->id]);
    Attendance::whereIn('lesson_id', Lesson::where('group_id', $group->id)->select('id'))->delete();
    Lesson::where('group_id', $group->id)->delete();
        Subject::where('group_id', $group->id)->delete();
        $group->delete();
    }

    Subject::all()
        ->filter(fn (Subject $subject) => preg_match('/[A-Za-z]/', $subject->name) === 1)
        ->each
        ->delete();
});

$studentsData = [
    ['РђР№СЃСѓР»Сѓ', 'РќСѓСЂР»Р°РЅРѕРІР°'],
    ['РђР»РёС…Р°РЅ', 'РЎРµСЂРёРєРѕРІ'],
    ['Р”РёР°РЅР°', 'РђС…РјРµС‚РѕРІР°'],
    ['Р•СЂР°СЃС‹Р»', 'РљР°СЃС‹РјРѕРІ'],
    ['РњР°РґРёРЅР°', 'РћРјР°СЂРѕРІР°'],
    ['РќСѓСЂРёСЃР»Р°Рј', 'Р•СЂРјРµРєРѕРІ'],
    ['РЎР°Р±РёРЅР°', 'РўР»РµСѓР±РµСЂРіРµРЅРѕРІР°'],
    ['Р РѕРјР°РЅ', 'РљРёРј'],
    ['РђРјРёРЅР°', 'РЎР°РґС‹РєРѕРІР°'],
    ['Р”Р°РјРёСЂ', 'Р–СѓРјР°Р±Р°РµРІ'],
    ['РљР°СЂРёРЅР°', 'Р’Р°СЃРёР»СЊРµРІР°'],
    ['РўРёРјСѓСЂ', 'РР±СЂР°РµРІ'],
    ['РђСЂСѓР¶Р°РЅ', 'Р‘РµРєР¶Р°РЅРѕРІР°'],
    ['РќРёРєРёС‚Р°', 'РњРѕСЂРѕР·РѕРІ'],
    ['РњРµСЂСѓРµСЂС‚', 'РђР±РґСѓР»Р»РёРЅР°'],
    ['РђСЂРјР°РЅ', 'РЎСѓР»РµР№РјРµРЅРѕРІ'],
    ['Р•РєР°С‚РµСЂРёРЅР°', 'РџР°РІР»РѕРІР°'],
    ['Р СѓСЃР»Р°РЅ', 'РњСѓС…Р°РјРµРґР¶Р°РЅРѕРІ'],
    ['Р–Р°РЅРµР»СЊ', 'РўРѕРєС‚Р°СЂРѕРІР°'],
    ['Р’Р»Р°РґРёСЃР»Р°РІ', 'РћСЂР»РѕРІ'],
    ['РђР»РёРЅР°', 'Р“СЂРѕРјРѕРІР°'],
    ['Р”Р°РЅРёСЏСЂ', 'Р Р°С…РёРјРѕРІ'],
    ['РЎРѕС„РёСЏ', 'Р›РµР±РµРґРµРІР°'],
    ['РР»СЊСЏ', 'РЎРјРёСЂРЅРѕРІ'],
    ['РќР°Р·РµСЂРєРµ', 'РљР°Р»РёРµРІР°'],
    ['РњР°РєСЃРёРј', 'Р—Р°Р№С†РµРІ'],
    ['РђСЂС‚РµРј', 'РќРѕРІРёРєРѕРІ'],
    ['РЇСЃРјРёРЅР°', 'РљСѓСЃР°РёРЅРѕРІР°'],
    ['Р’РёРєС‚РѕСЂРёСЏ', 'Р‘РµР»РѕРІР°'],
    ['Р РёРЅР°С‚', 'РЎР°РіС‹РЅРґС‹РєРѕРІ'],
    ['РњРёС…Р°РёР»', 'Р¤РµРґРѕСЂРѕРІ'],
    ['Р›Р°СѓСЂР°', 'РњСѓСЂР°С‚РѕРІР°'],
    ['РЎРµСЂРіРµР№', 'РљСѓР·РЅРµС†РѕРІ'],
    ['РђР»РёСЏ', 'РСЃРјР°РіСѓР»РѕРІР°'],
    ['РљРёСЂРёР»Р»', 'Р•РіРѕСЂРѕРІ'],
    ['РЎР°Р»С‚Р°РЅР°С‚', 'Р‘Р°Р№Р¶Р°РЅРѕРІР°'],
];

$groupList = array_values($groups);

foreach ($studentsData as $index => [$firstName, $lastName]) {
    $number = 'S-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
    $email = 'student' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) . '@journal.local';
    $group = $groupList[$index % count($groupList)];

    $user = User::updateOrCreate(
        ['email' => $email],
        [
            'name' => $firstName . ' ' . $lastName,
            'password' => Hash::make('password'),
            'role' => 'student',
        ]
    );

    Student::updateOrCreate(
        ['student_number' => $number],
        [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]
    );
}

Student::all()
    ->filter(fn (Student $student) => $hasLatin($student->first_name) || $hasLatin($student->last_name))
    ->each
    ->delete();

$subjectsData = [
    'РРЎ-21' => [
        ['РњР°С‚РµРјР°С‚РёРєР°', 'РљРІР°РґСЂР°С‚РЅС‹Рµ СѓСЂР°РІРЅРµРЅРёСЏ, С„СѓРЅРєС†РёРё Рё РїСЂР°РєС‚РёС‡РµСЃРєРёРµ Р·Р°РґР°С‡Рё'],
        ['Р‘Р°Р·С‹ РґР°РЅРЅС‹С…', 'РўР°Р±Р»РёС†С‹, СЃРІСЏР·Рё, Р·Р°РїСЂРѕСЃС‹ Рё РЅРѕСЂРјР°Р»РёР·Р°С†РёСЏ РґР°РЅРЅС‹С…'],
        ['Р’РµР±-СЂР°Р·СЂР°Р±РѕС‚РєР°', 'Laravel, РјР°СЂС€СЂСѓС‚С‹, С€Р°Р±Р»РѕРЅС‹ Рё Р»РёС‡РЅС‹Рµ РєР°Р±РёРЅРµС‚С‹'],
        ['РРЅС„РѕСЂРјР°С†РёРѕРЅРЅР°СЏ Р±РµР·РѕРїР°СЃРЅРѕСЃС‚СЊ', 'РџР°СЂРѕР»Рё, СЂРѕР»Рё, РґРѕСЃС‚СѓРї Рё Р·Р°С‰РёС‚Р° РґР°РЅРЅС‹С…'],
    ],
    'РџРћ-22' => [
        ['РђР»РіРѕСЂРёС‚РјС‹', 'Р›РѕРіРёРєР° РїСЂРѕРіСЂР°РјРјРёСЂРѕРІР°РЅРёСЏ, РјР°СЃСЃРёРІС‹ Рё СЃРѕСЂС‚РёСЂРѕРІРєРё'],
        ['РћСЃРЅРѕРІС‹ РїСЂРѕРіСЂР°РјРјРёСЂРѕРІР°РЅРёСЏ', 'РЎРёРЅС‚Р°РєСЃРёСЃ, С„СѓРЅРєС†РёРё Рё СЂР°Р±РѕС‚Р° СЃ РѕС€РёР±РєР°РјРё'],
        ['РљРѕРјРїСЊСЋС‚РµСЂРЅС‹Рµ СЃРµС‚Рё', 'IP-Р°РґСЂРµСЃР°С†РёСЏ, РїСЂРѕС‚РѕРєРѕР»С‹ Рё СЃРµС‚РµРІС‹Рµ СЃРµСЂРІРёСЃС‹'],
    ],
    'Р’Рў-23' => [
        ['РђСЂС…РёС‚РµРєС‚СѓСЂР° РєРѕРјРїСЊСЋС‚РµСЂР°', 'РЈСЃС‚СЂРѕР№СЃС‚РІРѕ РџРљ, РїР°РјСЏС‚СЊ Рё РїСЂРѕС†РµСЃСЃРѕСЂС‹'],
        ['РћРїРµСЂР°С†РёРѕРЅРЅС‹Рµ СЃРёСЃС‚РµРјС‹', 'Р¤Р°Р№Р»С‹, РїСЂРѕС†РµСЃСЃС‹, РїРѕР»СЊР·РѕРІР°С‚РµР»Рё Рё РїСЂР°РІР° РґРѕСЃС‚СѓРїР°'],
        ['Р­Р»РµРєС‚СЂРѕРЅРёРєР°', 'Р‘Р°Р·РѕРІС‹Рµ СЃС…РµРјС‹ Рё РёР·РјРµСЂРµРЅРёСЏ'],
    ],
    'Р”Р-21' => [
        ['UX/UI РґРёР·Р°Р№РЅ', 'РЎС†РµРЅР°СЂРёРё РїРѕР»СЊР·РѕРІР°С‚РµР»РµР№, РјР°РєРµС‚С‹ Рё РїСЂРѕС‚РѕС‚РёРїС‹'],
        ['Р“СЂР°С„РёС‡РµСЃРєРёР№ РґРёР·Р°Р№РЅ', 'РљРѕРјРїРѕР·РёС†РёСЏ, С†РІРµС‚, С‚РёРїРѕРіСЂР°С„РёРєР° Рё СЃРµС‚РєРё'],
        ['РџСЂРѕРµРєС‚РЅР°СЏ РїСЂР°РєС‚РёРєР°', 'РљРѕРјР°РЅРґРЅР°СЏ СЂР°Р±РѕС‚Р° Рё РѕС„РѕСЂРјР»РµРЅРёРµ СЂРµР·СѓР»СЊС‚Р°С‚Р°'],
    ],
];

foreach ($subjectsData as $groupName => $subjects) {
    $group = $groups[$groupName];

    foreach ($subjects as [$name, $description]) {
        $subject = Subject::updateOrCreate(
            ['name' => $name, 'group_id' => $group->id],
            [
                'teacher_id' => $teacher->id,
                'description' => $description,
            ]
        );

        $topics = [
            'Р’РІРѕРґРЅРѕРµ Р·Р°РЅСЏС‚РёРµ Рё РїСЂР°РІРёР»Р° СЂР°Р±РѕС‚С‹',
            'РџСЂР°РєС‚РёС‡РµСЃРєРѕРµ Р·Р°РґР°РЅРёРµ РїРѕ С‚РµРјРµ',
            'Р—Р°РєСЂРµРїР»РµРЅРёРµ РјР°С‚РµСЂРёР°Р»Р° Рё РїСЂРѕРІРµСЂРѕС‡РЅР°СЏ СЂР°Р±РѕС‚Р°',
        ];

        foreach ($topics as $lessonIndex => $topic) {
            $date = now()->subDays(12 - $lessonIndex * 3)->toDateString();

            $lesson = Lesson::updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'group_id' => $group->id,
                    'date' => $date,
                    'topic' => $topic,
                ],
                [
                    'teacher_id' => $teacher->id,
                    'description' => 'Р”РµРјРѕРЅСЃС‚СЂР°С†РёРѕРЅРЅС‹Р№ СѓСЂРѕРє РґР»СЏ РїСЂРѕРІРµСЂРєРё Р¶СѓСЂРЅР°Р»Р°.',
                ]
            );

            $students = Student::where('group_id', $group->id)->orderBy('id')->get();

            foreach ($students as $studentIndex => $student) {
            $status = ($studentIndex % 7 === 0) ? 'absent' : 'present';

                Attendance::updateOrCreate(
                    ['lesson_id' => $lesson->id, 'student_id' => $student->id],
                    [
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'date' => $date,
                        'status' => $status,
                        'note' => $status === 'present' ? null : 'Р”РµРјРѕ-РѕС‚РјРµС‚РєР°',
                    ]
                );

            }
        }
    }
}

Subject::all()
    ->filter(fn (Subject $subject) => $hasLatin($subject->name))
    ->each
    ->delete();

$englishGroupsLeft = Group::all()->filter(fn (Group $group) => $hasLatin($group->name))->count();
$englishSubjectsLeft = Subject::all()->filter(fn (Subject $subject) => $hasLatin($subject->name))->count();
$englishStudentsLeft = Student::all()
    ->filter(fn (Student $student) => $hasLatin($student->first_name) || $hasLatin($student->last_name))
    ->count();

echo 'Groups: ' . Group::count() . PHP_EOL;
echo 'Students: ' . Student::count() . PHP_EOL;
echo 'Subjects: ' . Subject::count() . PHP_EOL;
echo 'Lessons: ' . Lesson::count() . PHP_EOL;
echo 'English groups left: ' . $englishGroupsLeft . PHP_EOL;
echo 'English subjects left: ' . $englishSubjectsLeft . PHP_EOL;
echo 'English students left: ' . $englishStudentsLeft . PHP_EOL;
