<?php

use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Carbon;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$topics = [
    'Р’РІРѕРґРЅРѕРµ Р·Р°РЅСЏС‚РёРµ Рё РїСЂР°РІРёР»Р° СЂР°Р±РѕС‚С‹',
    'РџРѕРІС‚РѕСЂРµРЅРёРµ Р±Р°Р·РѕРІС‹С… РїРѕРЅСЏС‚РёР№',
    'РџСЂР°РєС‚РёС‡РµСЃРєРѕРµ Р·Р°РґР°РЅРёРµ РїРѕ С‚РµРјРµ',
    'Р Р°Р±РѕС‚Р° СЃ РїСЂРёРјРµСЂР°РјРё',
    'Р Р°Р·Р±РѕСЂ С‚РёРїРѕРІС‹С… РѕС€РёР±РѕРє',
    'РЎР°РјРѕСЃС‚РѕСЏС‚РµР»СЊРЅР°СЏ СЂР°Р±РѕС‚Р°',
    'Р“СЂСѓРїРїРѕРІРѕРµ РїСЂР°РєС‚РёС‡РµСЃРєРѕРµ Р·Р°РґР°РЅРёРµ',
    'РљРѕРЅС‚СЂРѕР»СЊРЅР°СЏ С‚РѕС‡РєР°',
    'Р—Р°РєСЂРµРїР»РµРЅРёРµ РјР°С‚РµСЂРёР°Р»Р°',
    'Р Р°Р±РѕС‚Р° СЃ СѓС‡РµР±РЅС‹Рј РєРµР№СЃРѕРј',
    'РњРёРЅРё-РїСЂРѕРµРєС‚ РїРѕ С‚РµРјРµ',
    'РўСЂРµРЅРёСЂРѕРІРѕС‡РЅС‹Рµ Р·Р°РґР°РЅРёСЏ',
    'Р Р°Р·Р±РѕСЂ РґРѕРјР°С€РЅРµР№ СЂР°Р±РѕС‚С‹',
    'РџСЂР°РєС‚РёРєСѓРј СЃ РїСЂРѕРІРµСЂРєРѕР№',
    'РџРѕРґРіРѕС‚РѕРІРєР° Рє РїСЂРѕРІРµСЂРѕС‡РЅРѕР№ СЂР°Р±РѕС‚Рµ',
    'РџСЂРѕРІРµСЂРѕС‡РЅР°СЏ СЂР°Р±РѕС‚Р°',
    'РђРЅР°Р»РёР· СЂРµР·СѓР»СЊС‚Р°С‚РѕРІ',
    'Р”РѕРїРѕР»РЅРёС‚РµР»СЊРЅР°СЏ РїСЂР°РєС‚РёРєР°',
    'РС‚РѕРіРѕРІРѕРµ РїРѕРІС‚РѕСЂРµРЅРёРµ',
    'РС‚РѕРіРѕРІРѕРµ Р·Р°РЅСЏС‚РёРµ РїРѕ СЂР°Р·РґРµР»Сѓ',
];

$createdLessons = 0;
$createdAttendances = 0;

Subject::with('group')->orderBy('group_id')->orderBy('name')->get()->each(function (Subject $subject) use ($topics, &$createdLessons, &$createdAttendances) {
    $existingCount = Lesson::where('subject_id', $subject->id)->count();

    for ($index = $existingCount; $index < 20; $index++) {
        $date = Carbon::now()->subDays((20 - $index) * 3)->toDateString();
        $topic = $topics[$index % count($topics)];

        $lesson = Lesson::firstOrCreate(
            [
                'subject_id' => $subject->id,
                'group_id' => $subject->group_id,
                'teacher_id' => $subject->teacher_id,
                'date' => $date,
                'topic' => $topic,
            ],
            [
                'description' => 'Р”РµРјРѕРЅСЃС‚СЂР°С†РёРѕРЅРЅС‹Р№ СѓСЂРѕРє РґР»СЏ РїСЂРѕРІРµСЂРєРё Р¶СѓСЂРЅР°Р»Р°.',
            ]
        );

        if ($lesson->wasRecentlyCreated) {
            $createdLessons++;
        }

        $students = Student::where('group_id', $subject->group_id)->orderBy('last_name')->orderBy('first_name')->get();

        foreach ($students as $studentIndex => $student) {
            $status = (($studentIndex + $index) % 8 === 0) ? 'absent' : 'present';

            $attendance = Attendance::firstOrCreate(
                [
                    'lesson_id' => $lesson->id,
                    'student_id' => $student->id,
                ],
                [
                    'subject_id' => $subject->id,
                    'teacher_id' => $subject->teacher_id,
                    'date' => $date,
                    'status' => $status,
                    'note' => $status === 'present' ? null : 'Р”РµРјРѕ-РѕС‚РјРµС‚РєР°',
                ]
            );

            if ($attendance->wasRecentlyCreated) {
                $createdAttendances++;
            }

        }
    }
});

echo 'Subjects: ' . Subject::count() . PHP_EOL;
echo 'Lessons total: ' . Lesson::count() . PHP_EOL;
echo 'Created lessons: ' . $createdLessons . PHP_EOL;
echo 'Created attendances: ' . $createdAttendances . PHP_EOL;
