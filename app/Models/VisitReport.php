<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitReport extends Model
{
    protected $table = 'report_visits';

    protected $fillable = [
        'trader_id',
        'plumber_id',
        'customer_name',
        'company_name',
        'location',
        'region_province',
        'phone',
        'email',
        'client_type',
        'visit_type',
        'visit_result',
        'interest_level',
        'purchase_readiness',
        'authority_level',
        'sales_value',
        'planned_purchase_date',
        'outcome_classification',
        'next_action',
        'sales_classification',
        'status',
        'additional_notes',
        'photos',
    ];

    protected $casts = [
        'planned_purchase_date' => 'date',
        'photos' => 'array',
        'sales_value' => 'decimal:2',
    ];

    public function trader()
    {
        return $this->belongsTo(Trader::class, 'trader_id');
    }

    public function plumber()
    {
        return $this->belongsTo(Plumber::class, 'plumber_id');
    }

    public function inspectionVisit()
    {
        return $this->hasOne(InspectionVisit::class, 'report_id');
    }
}
