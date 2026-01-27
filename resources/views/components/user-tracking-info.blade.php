{{-- User Tracking Information Component --}}
{{-- 
    Usage: @include('components.user-tracking-info', ['model' => $student])
    
    This component displays who created and updated a record
--}}

@props(['model'])

<div class="user-tracking-info text-sm text-gray-600">
    <div class="grid grid-cols-2 gap-4">
        @if($model->created_by)
        <div class="tracking-item">
            <span class="font-semibold">Created by:</span>
            <span>{{ $model->creator->name ?? 'Unknown' }}</span>
            <br>
            <span class="text-xs">{{ $model->created_at?->format('M d, Y H:i') }}</span>
        </div>
        @endif

        @if($model->updated_by)
        <div class="tracking-item">
            <span class="font-semibold">Last updated by:</span>
            <span>{{ $model->updater->name ?? 'Unknown' }}</span>
            <br>
            <span class="text-xs">{{ $model->updated_at?->format('M d, Y H:i') }}</span>
        </div>
        @endif

        @if($model->deleted_by && $model->deleted_at)
        <div class="tracking-item">
            <span class="font-semibold">Deleted by:</span>
            <span>{{ $model->deleter->name ?? 'Unknown' }}</span>
            <br>
            <span class="text-xs">{{ $model->deleted_at?->format('M d, Y H:i') }}</span>
        </div>
        @endif
    </div>
</div>
