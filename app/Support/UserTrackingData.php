<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class UserTrackingData
{
    public static function forCreate(): array
    {
        $userId = Auth::id();

        return $userId === null ? [] : [
            'created_by' => $userId,
            'updated_by' => $userId,
        ];
    }

    public static function forUpdate(): array
    {
        $userId = Auth::id();

        return $userId === null ? [] : [
            'updated_by' => $userId,
        ];
    }
}
