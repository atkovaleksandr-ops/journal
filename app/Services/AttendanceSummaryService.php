<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Collection;

class AttendanceSummaryService
{
    public function forStudent(Student $student, bool $withLessonHistory = false): array
    {
        $student->loadMissing('group');

        $subjects = Subject::where('group_id', $student->group_id)
            ->when($withLessonHistory, fn ($query) => $query->with([
                'lessons' => fn ($lessonQuery) => $lessonQuery->latest('date')->latest('id'),
            ]))
            ->withCount('lessons')
            ->orderBy('name')
            ->get();

        $attendancesBySubject = Attendance::where('student_id', $student->id)
            ->whereIn('subject_id', $subjects->pluck('id'))
            ->get()
            ->groupBy('subject_id');

        $subjectSummaries = $subjects->map(function (Subject $subject) use ($attendancesBySubject, $withLessonHistory) {
            $attendances = $attendancesBySubject->get($subject->id, collect());
            $attendancesByLesson = $attendances->keyBy('lesson_id');
            $present = $attendances->where('status', Attendance::STATUS_PRESENT)->count();
            $absent = $attendances->where('status', Attendance::STATUS_ABSENT)->count();
            $marked = $present + $absent;
            $lessons = $subject->lessons_count;

            $summary = [
                'subject' => $subject,
                'lessons_count' => $lessons,
                'marked_count' => $marked,
                'present_count' => $present,
                'absent_count' => $absent,
                'not_marked_count' => max($lessons - $marked, 0),
                'attendance_percent' => $lessons > 0 ? (int) round($present / $lessons * 100) : null,
            ];

            if ($withLessonHistory) {
                $summary['lesson_history'] = $subject->lessons->map(
                    fn (Lesson $lesson) => $this->lessonHistoryItem($lesson, $attendancesByLesson->get($lesson->id))
                );
            }

            return $summary;
        })->values();

        return [
            'subjects' => $subjects,
            'subjectSummaries' => $subjectSummaries,
            'summary' => $this->overallSummary($subjectSummaries),
        ];
    }

    private function lessonHistoryItem(Lesson $lesson, ?Attendance $attendance): array
    {
        $status = $attendance?->status;

        return [
            'lesson' => $lesson,
            'attendance' => $attendance,
            'status' => in_array($status, Attendance::VALID_STATUSES, true) ? $status : 'not_marked',
            'note' => $attendance?->note,
        ];
    }

    private function overallSummary(Collection $subjectSummaries): array
    {
        $lessons = $subjectSummaries->sum('lessons_count');
        $present = $subjectSummaries->sum('present_count');
        $absent = $subjectSummaries->sum('absent_count');
        $marked = $present + $absent;

        return [
            'lessons' => $lessons,
            'present' => $present,
            'absent' => $absent,
            'marked' => $marked,
            'not_marked' => max($lessons - $marked, 0),
            'attendance_percent' => $lessons > 0 ? (int) round($present / $lessons * 100) : null,
        ];
    }
}
