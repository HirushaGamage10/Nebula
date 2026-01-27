<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Trait UserTracking
 * 
 * Automatically tracks which user created or updated a record.
 * Adds created_by and updated_by functionality to models.
 */
trait UserTracking
{
    /**
     * Boot the user tracking trait for a model.
     */
    protected static function bootUserTracking()
    {
        // Set created_by on creating
        static::creating(function ($model) {
            if (Auth::check() && !$model->isDirty('created_by')) {
                // Get the user's primary key value (handles both 'id' and 'user_id')
                $userId = Auth::user()->getKey();
                $model->created_by = $userId;
            }
            
            // Also set updated_by on creation
            if (Auth::check() && !$model->isDirty('updated_by')) {
                $userId = Auth::user()->getKey();
                $model->updated_by = $userId;
            }
        });

        // Set updated_by on updating
        static::updating(function ($model) {
            if (Auth::check() && !$model->isDirty('updated_by')) {
                $userId = Auth::user()->getKey();
                $model->updated_by = $userId;
            }
        });

        // Optional: Set deleted_by on soft delete if the column exists
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(get_called_class()))) {
            static::deleting(function ($model) {
                if (Auth::check() && $model->isSoftDeleteEnabled() && !$model->isDirty('deleted_by')) {
                    $userId = Auth::user()->getKey();
                    $model->deleted_by = $userId;
                    $model->save();
                }
            });
        }
    }

    /**
     * Check if soft deletes are enabled
     */
    protected function isSoftDeleteEnabled()
    {
        return property_exists($this, 'forceDeleting');
    }

    /**
     * Get the user who created this record
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'user_id');
    }

    /**
     * Get the user who last updated this record
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by', 'user_id');
    }

    /**
     * Get the user who deleted this record (if soft deletes are enabled)
     */
    public function deleter()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by', 'user_id');
    }
}
