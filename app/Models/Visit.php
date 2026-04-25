<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    /**
     * @var string
     */
    protected $table = 'visits';

    /**
     * @var string[]
     */
    protected $fillable = [
        'area_id',
        'beat_id',
        'customer_id',
        'creator_id',
        'employee_id',
        'visit_date',
        'visit_time',
        'description',
        'image',
    ];

    /**
     * @return BelongsTo
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * @return BelongsTo
     */
    public function beat()
    {
        return $this->belongsTo(Beat::class, 'beat_id');
    }

    /**
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * @return string|null
     */
    public function getVisitDateFormattedAttribute()
    {
        return $this->visit_date ? date('d-m-Y', strtotime($this->visit_date)) : null;
    }

    /**
     * @return string|null
     */
    public function getVisitTimeFormattedAttribute()
    {
        return $this->visit_time ? date('H:i', strtotime($this->visit_time)) : null;
    }
}
