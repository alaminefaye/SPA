<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeAttendance extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'employee_id',
        'date',
        'arrival_time',
        'departure_time',
        'present',
        'notes'
    ];
    
    protected $casts = [
        'date' => 'date',
        'arrival_time' => 'string',
        'departure_time' => 'string',
        'present' => 'boolean'
    ];
    
    /**
     * Configure logging options for activity log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'employee_id', 'date', 'arrival_time', 'departure_time', 'present', 'notes'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    /**
     * Get the employee that owns the attendance record
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
