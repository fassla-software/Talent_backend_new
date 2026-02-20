<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionVisit extends Model
{
    protected $table = 'inspection_visits';

    protected $fillable = [
        'inspector_id',
        'trader_id',
        'plumber_id',
        'report_id',
        'status',
        'check_in_at',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_at',
        'check_out_latitude',
        'check_out_longitude',
        'scheduled_at',
        'visit_type',
        'notes',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function trader()
    {
        return $this->belongsTo(Trader::class, 'trader_id');
    }

    public function plumber()
    {
        return $this->belongsTo(Plumber::class, 'plumber_id');
    }

    public function visitReport()
    {
        return $this->belongsTo(VisitReport::class, 'report_id');
    }
}
