<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Group;
use App\Models\Lesson;
use App\Models\Subject;
use App\Services\AttendanceSummaryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function show(Request $request, $groupId)
    {
        $group = Group::with('students')->findOrFail($groupId);
        $studentsCount = $group->students->count();

        $subjects = Subject::where('group_id', $group->id)
            ->where('teacher_id', auth()->id())
            ->orderBy('name')
            ->get();

        $filters = [
            'subject_id' => $request->query('subject_id'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
            'status' => $request->query('status'),
            'q' => trim((string) $request->query('q', '')),
            'sort' => $request->query('sort', 'date_desc'),
        ];

        if (!in_array($filters['status'], ['', 'filled', 'has_absent'], true)) {
            $filters['status'] = '';
        }

        $lessonsQuery = Lesson::with('subject')
            ->withCount([
                'attendances',
                'attendances as present_count' => fn ($query) => $query->present(),
                'attendances as absent_count' => fn ($query) => $query->absent(),
            ])
            ->where('group_id', $group->id)
            ->where('teacher_id', auth()->id());

        if ($filters['subject_id']) {
            $lessonsQuery->where('subject_id', $filters['subject_id']);
        }

        if ($filters['date_from']) {
            $lessonsQuery->whereDate('date', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $lessonsQuery->whereDate('date', '<=', $filters['date_to']);
        }

        if ($filters['q'] !== '') {
            $search = $filters['q'];
            $lessonsQuery->where(function ($query) use ($search) {
                $query->where('topic', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('subject', fn ($subjectQuery) => $subjectQuery->where('name', 'like', "%{$search}%"));
            });
        }

        match ($filters['sort']) {
            'date_asc' => $lessonsQuery->orderBy('date')->orderBy('id'),
            'subject' => $lessonsQuery->orderBy(
                Subject::select('name')->whereColumn('subjects.id', 'lessons.subject_id')->limit(1)
            )->latest('lessons.date'),
            'progress' => $lessonsQuery->orderByDesc('attendances_count')->latest('date'),
            default => $lessonsQuery->latest('date')->latest('id'),
        };

        $allLessons = $lessonsQuery->get();
        $lessons = $allLessons->filter(function ($lesson) use ($filters, $studentsCount) {
            if ($filters['status'] === 'filled') {
                return $studentsCount > 0 && $lesson->attendances_count >= $studentsCount;
            }

            if ($filters['status'] === 'has_absent') {
                return $lesson->absent_count > 0;
            }

            return true;
        })->values();

        $lessonsBySubject = $lessons->groupBy(fn ($lesson) => $lesson->subject->name ?? 'Без предмета');
        $selectedSubject = $subjects->firstWhere('id', (int) $filters['subject_id']);
        $subjectSummaries = $subjects->map(function ($subject) use ($lessons, $studentsCount) {
            $subjectLessons = $lessons->where('subject_id', $subject->id);

            return [
                'subject' => $subject,
                'lessons' => $subjectLessons->count(),
                'filled' => $subjectLessons->filter(
                    fn ($lesson) => $studentsCount > 0 && $lesson->attendances_count >= $studentsCount
                )->count(),
                'present' => $subjectLessons->sum('present_count'),
                'absent' => $subjectLessons->sum('absent_count'),
                'not_marked' => $subjectLessons->sum(
                    fn ($lesson) => max($studentsCount - $lesson->attendances_count, 0)
                ),
            ];
        })->filter(fn ($summary) => $summary['lessons'] > 0 || request('subject_id') === null)->values();

        $summary = [
            'lessons' => $lessons->count(),
            'subjects' => $lessonsBySubject->count(),
            'filled' => $lessons->filter(
                fn ($lesson) => $studentsCount > 0 && $lesson->attendances_count >= $studentsCount
            )->count(),
            'present' => $lessons->sum('present_count'),
            'absent' => $lessons->sum('absent_count'),
            'not_marked' => $lessons->sum(fn ($lesson) => max($studentsCount - $lesson->attendances_count, 0)),
            'students' => $studentsCount,
        ];

        return view('attendance.show', compact(
            'group',
            'subjects',
            'lessons',
            'lessonsBySubject',
            'filters',
            'summary',
            'studentsCount',
            'selectedSubject',
            'subjectSummaries'
        ));
    }

    public function storeLesson(Request $request)
    {
        $validated = $request->validate([
            'group_id' => ['required', 'exists:groups,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'date' => ['required', 'date'],
            'topic' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'return_to' => ['nullable', 'string'],
        ]);

        $subject = Subject::where('id', $validated['subject_id'])
            ->where('group_id', $validated['group_id'])
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $exists = Lesson::where('group_id', $validated['group_id'])
            ->where('subject_id', $subject->id)
            ->where('teacher_id', auth()->id())
            ->where('date', $validated['date'])
            ->where('topic', $validated['topic'] ?? null)
            ->exists();

        if ($exists) {
            return back()->withErrors(['lesson' => 'Такой урок уже существует.'])->withInput();
        }

        $lesson = Lesson::create([
            'group_id' => $validated['group_id'],
            'subject_id' => $subject->id,
            'teacher_id' => auth()->id(),
            'date' => $validated['date'],
            'topic' => $validated['topic'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('attendance.lesson.mark', [
            'lesson' => $lesson,
            'return_to' => $validated['return_to'] ?? route('groups.attendance', [
                'group' => $lesson->group_id,
                'subject_id' => $lesson->subject_id,
            ], false),
        ])->with('success', 'Урок создан. Отметьте посещаемость студентов.');
    }

    public function markForm(Lesson $lesson)
    {
        abort_unless($lesson->teacher_id === auth()->id(), 403);

        $lesson->load('group.students', 'subject');
        $attendances = Attendance::where('lesson_id', $lesson->id)->get()->keyBy('student_id');

        return view('attendance.mark', compact('lesson', 'attendances'));
    }

    public function mark(Request $request, Lesson $lesson)
    {
        abort_unless($lesson->teacher_id === auth()->id(), 403);

        $returnTo = $this->safeReturnTo($request->input('return_to'), $lesson);
        $data = $request->validate([
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', Rule::in(Attendance::VALID_STATUSES)],
            'attendance.*.note' => ['nullable', 'string', 'max:500'],
            'return_to' => ['nullable', 'string'],
        ]);

        $studentIds = $lesson->group->students()->pluck('students.id');

        foreach ($studentIds as $studentId) {
            $status = $data['attendance'][$studentId]['status'] ?? Attendance::STATUS_PRESENT;

            $note = trim((string) ($data['attendance'][$studentId]['note'] ?? ''));

            Attendance::updateOrCreate(
                ['lesson_id' => $lesson->id, 'student_id' => $studentId],
                [
                    'subject_id' => $lesson->subject_id,
                    'teacher_id' => auth()->id(),
                    'date' => $lesson->date,
                    'status' => $status,
                    'note' => $note !== '' ? $note : null,
                ]
            );
        }

        return redirect($returnTo)->with('success', 'Посещаемость сохранена.');
    }

    public function history(AttendanceSummaryService $attendanceSummaryService)
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Ваш аккаунт пока не привязан к студенту.');
        }

        $attendanceData = $attendanceSummaryService->forStudent($student);
        $subjects = $attendanceData['subjects'];
        $subjectSummaries = $attendanceData['subjectSummaries'];

        return view('attendance.history', compact('student', 'subjects', 'subjectSummaries'));
    }

    public function historySubject(Request $request, Subject $subject)
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Ваш аккаунт пока не привязан к студенту.');
        }

        abort_unless($subject->group_id === $student->group_id, 403);

        $filters = [
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
            'status' => $request->query('status', ''),
            'q' => trim((string) $request->query('q', '')),
            'sort' => $request->query('sort', 'date_desc'),
        ];

        if (!in_array($filters['status'], ['', ...Attendance::VALID_STATUSES, 'not_marked'], true)) {
            $filters['status'] = '';
        }

        $allLessons = Lesson::with(['attendances' => function ($query) use ($student) {
            $query->where('student_id', $student->id);
        }])
            ->where('group_id', $student->group_id)
            ->where('subject_id', $subject->id)
            ->latest('date')
            ->latest('id')
            ->get();

        $allAttendances = $allLessons->map(fn ($lesson) => $lesson->attendances->first())->filter();
        $present = $allAttendances->where('status', Attendance::STATUS_PRESENT)->count();
        $absent = $allAttendances->where('status', Attendance::STATUS_ABSENT)->count();
        $marked = $present + $absent;

        $summary = [
            'lessons' => $allLessons->count(),
            'marked' => $marked,
            'present' => $present,
            'absent' => $absent,
            'not_marked' => max($allLessons->count() - $marked, 0),
            'attendance_percent' => $allLessons->count() > 0
                ? (int) round($present / $allLessons->count() * 100)
                : null,
        ];

        $lessons = $allLessons->filter(function ($lesson) use ($filters) {
            $attendance = $lesson->attendances->first();

            if ($filters['date_from'] && $lesson->date < $filters['date_from']) {
                return false;
            }
            if ($filters['date_to'] && $lesson->date > $filters['date_to']) {
                return false;
            }
            if ($filters['status'] !== '') {
                $status = $attendance->status ?? 'not_marked';
                if ($status !== $filters['status']) {
                    return false;
                }
            }

            $searchText = mb_strtolower(trim(($lesson->topic ?? '') . ' ' . ($lesson->description ?? '')));
            return $filters['q'] === '' || str_contains($searchText, mb_strtolower($filters['q']));
        });

        $lessons = $filters['sort'] === 'date_asc'
            ? $lessons->sortBy([['date', 'asc'], ['id', 'asc']])->values()
            : $lessons->sortByDesc('id')->sortByDesc('date')->values();

        return view('attendance.subject', compact('student', 'subject', 'lessons', 'summary', 'filters'));
    }

    public function destroyLesson(Lesson $lesson)
    {
        abort_unless($lesson->teacher_id === auth()->id(), 403);

        $returnTo = $this->safeReturnTo(request('return_to'), $lesson);
        $lesson->delete();

        return redirect($returnTo)->with('success', 'Урок удалён.');
    }

    private function safeReturnTo(?string $returnTo, Lesson $lesson): string
    {
        $fallback = route('groups.attendance', [
            'group' => $lesson->group_id,
            'subject_id' => $lesson->subject_id,
        ], false);

        if (!$returnTo) {
            return $fallback;
        }

        $host = parse_url($returnTo, PHP_URL_HOST);
        if ($host && $host !== request()->getHost()) {
            return $fallback;
        }

        return $returnTo;
    }
}
