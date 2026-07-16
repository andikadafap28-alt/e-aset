<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $oldData = [];
            $newData = [];

            foreach ($model->getChanges() as $key => $value) {
                if ($key !== 'updated_at') {
                    $oldData[$key] = $model->getOriginal($key);
                    $newData[$key] = $value;
                }
            }

            if (!empty($newData)) {
                $model->logActivity('updated', $oldData, $newData);
            }
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted', $model->getAttributes(), null);
        });
    }

    protected function logActivity($action, $oldData = null, $newData = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(), // Will be null if triggered via console/cron
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip(),
        ]);
    }
}
