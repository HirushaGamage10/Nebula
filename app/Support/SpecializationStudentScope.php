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

        $studentIds = collect();

        if (Schema::hasColumn('course_registration', 'specialization')) {
            $studentIds = $studentIds->merge(
                self::baseQuery('course_registration', $courseId, $intakeId, $location)
                    ->where('specialization', $specialization)
                    ->pluck('student_id')
            );
        }

        if (Schema::hasColumn('semester_registrations', 'specialization')) {
            $studentIds = $studentIds->merge(
                self::baseQuery('semester_registrations', $courseId, $intakeId, $location)
                    ->where('specialization', $specialization)
                    ->pluck('student_id')
            );
        }

        if (Schema::hasColumn('module_management', 'specialization')) {
            $studentIds = $studentIds->merge(
                self::baseQuery('module_management', $courseId, $intakeId, $location)
                    ->where('specialization', $specialization)
                    ->pluck('student_id')
            );
        }

        return $studentIds
            ->filter(fn ($studentId) => $studentId !== null && $studentId !== '')
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