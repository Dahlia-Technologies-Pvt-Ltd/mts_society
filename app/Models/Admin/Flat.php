<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin\{Floor};
use Illuminate\Database\Eloquent\SoftDeletes;


class Flat extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['flat_name', 'floor_id', 'status', 'created_by', 'updated_by'];

    function floor()
    {
        return $this->belongsTo(Floor::class);
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
