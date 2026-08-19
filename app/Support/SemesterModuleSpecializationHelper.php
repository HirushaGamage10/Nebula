<?php

namespace App\Support;

class SemesterModuleSpecializationHelper
{
    public static function normalizePayload(array $module): array
    {
        $moduleId = (string) ($module['module_id'] ?? '');
        $specializationsJson = null;
        $legacySpecialization = null;

        if (isset($module['specializations']) && is_array($module['specializations'])) {
            $specs = array_values(array_filter($module['specializations'], function ($value) {
                return is_string($value) && trim($value) !== '';
            }));

            if (count($specs) === 1) {
                $legacySpecialization = $specs[0];
                $specializationsJson = json_encode($specs);
            } elseif (count($specs) > 1) {
                $specializationsJson = json_encode($specs);
                $legacySpecialization = null;
            }
        } elseif (!empty($module['specialization'])) {
            $legacySpecialization = (string) $module['specialization'];
            $specializationsJson = json_encode([$legacySpecialization]);
        }

        return [
            'module_id' => $moduleId,
            'specializations' => $specializationsJson,
            'specialization' => $legacySpecialization,
        ];
    }

    public static function decodeList(?string $specializationsJson, ?string $legacySpecialization = null): ?array
    {
        if ($specializationsJson !== null && $specializationsJson !== '') {
            $specs = json_decode($specializationsJson, true);

            if (is_array($specs)) {
                $specs = array_values(array_filter($specs, fn ($value) => is_string($value) && trim($value) !== ''));

                return count($specs) > 0 ? $specs : null;
            }
        }

        if ($legacySpecialization !== null && $legacySpecialization !== '' && $legacySpecialization !== 'General') {
            return [$legacySpecialization];
        }

        return null;
    }

    public static function appliesTo(?string $specializationsJson, ?string $legacySpecialization, ?string $studentSpecialization): bool
    {
        $specs = self::decodeList($specializationsJson, $legacySpecialization);

        if ($specs === null) {
            return true;
        }

        if (!$studentSpecialization) {
            return false;
        }

        return in_array($studentSpecialization, $specs, true);
    }

    public static function matchesSelection(?string $specializationsJson, ?string $legacySpecialization = null, ?string $selectedSpecialization = null): bool
    {
        $selectedSpecialization = trim((string) ($selectedSpecialization ?? ''));

        if ($selectedSpecialization === '') {
            return true;
        }

        if (strcasecmp($selectedSpecialization, 'Common') === 0) {
            // "Common" modules are those with:
            //  - NO specialization assigned (null specs) → common to all students, OR
            //  - Assigned to MULTIPLE specializations → shared across a few specializations
            $specs = self::decodeList($specializationsJson, $legacySpecialization);

            if ($specs === null) {
                return true; // No specialization = applies to everyone
            }

            return count($specs) > 1; // Multiple specializations = shared/common module
        }

        $specs = self::decodeList($specializationsJson, $legacySpecialization);

        if ($specs === null) {
            return false;
        }

        return in_array($selectedSpecialization, $specs, true);
    }

    public static function displayLabel(?string $specializationsJson, ?string $legacySpecialization = null): string
    {
        $specs = self::decodeList($specializationsJson, $legacySpecialization);

        if ($specs === null) {
            return 'All Specializations';
        }

        return implode(', ', $specs);
    }
}
