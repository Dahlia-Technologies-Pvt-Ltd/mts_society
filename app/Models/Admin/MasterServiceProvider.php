<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin\{Flat,Parking};
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterServiceProvider extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['societies_id', 'name', 'is_daily_helper',
    'status', 'created_by', 'updated_by'];
    // getVehicleTypeValueAttribute
    public function getIsDailyHelperValueAttribute($data)
    {
        if (!isset($this->attributes['is_daily_helper'])) {
            return '';
        }else if($this->attributes['is_daily_helper'] == 0){
        return 'NO help';
    }
    else{
        return 'Yes help';
    }
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
