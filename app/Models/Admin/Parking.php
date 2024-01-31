<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin\{Floor,Tower,Wing,Flat};
use Illuminate\Database\Eloquent\SoftDeletes;

class Parking extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'societies_id', 'parking_type', 'vehicle_type', 'tower_id', 'wing_id', 'floor_id', 'flat_id', 'parking_area_number',
        'status', 'created_by', 'updated_by'
    ];

    function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    function flat()
    {
        return $this->belongsTo(Flat::class);
    }

    function tower()
    {
        return $this->belongsTo(Tower::class);
    }

    function wing()
    {
        return $this->belongsTo(Wing::class);
    }

    public function getCreatedAtAttribute($data)
    {
        if (!isset($this->attributes['created_at'])) {
            return '';
        }
        return Carbon::parse($this->attributes['created_at'])->format(config('util.default_date_time_format'));
    }
    public function getUpdatedAtAttribute($data)
    {
        if (!isset($this->attributes['updated_at'])) {
            return '';
        }
        return Carbon::parse($this->attributes['updated_at'])->format(config('util.default_date_time_format'));
    }
}
