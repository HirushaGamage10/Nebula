<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SpecializationStudentScope
{
    public static function normalize($specialization): ?string
    {
        if (!is_string($specialization)) {
            return null;
        }

        $specialization = trim($specialization);

        return $specialization === '' ? null : $specialization;
    }

    public static function resolveStudentIds(?int $courseId, ?int $intakeId, ?string $location, ?string $specialization): array
    {
        $specialization = self::normalize($specialization);

        if ($specialization === null || $courseId === null) {
            return [];
        }

        if (!Schema::hasTable('specialization_registrations')) {
            return [];
        }

        return self::baseQuery('specialization_registrations', $courseId, $intakeId, $location)
            ->where('specialization', $specialization)
            ->where('status', 'registered')
            ->pluck('student_id')
            ->unique()
            ->values()
            ->all();
    }

    public static function applyToQuery($query, string $studentColumn, ?int $courseId, ?int $intakeId, ?string $location, ?string $specialization)
    {
        $studentIds = self::resolveStudentIds($courseId, $intakeId, $location, $specialization);

        if (empty($studentIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($studentColumn, $studentIds);
    }

    private static function baseQuery(string $table, ?int $courseId, ?int $intakeId, ?string $location)
    {
        return DB::table($table)
            ->when($courseId !== null, function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->when($intakeId !== null, function ($query) use ($intakeId) {
                $query->where('intake_id', $intakeId);
            })
            ->when($location !== null && $location !== '', function ($query) use ($location) {
                $query->where('location', $location);
            });
    }
}
