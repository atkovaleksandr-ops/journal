<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const VALID_STATUSES = [
        self::STATUS_PRESENT,
        self::STATUS_ABSENT,
    ];

    protected $fillable = [
        'lesson_id',
        'student_id',
        'subject_id',
        'teacher_id',
        'date',
        'status',
        'note',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PRESENT => 'Присутствовал',
            self::STATUS_ABSENT => 'Отсутствовал',
        ];
    }

    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', self::STATUS_ABSENT);
    }

    public function isPresent(): bool
    {
        return $this->status === self::STATUS_PRESENT;
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
