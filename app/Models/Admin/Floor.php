<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin\{Flat};
use Illuminate\Database\Eloquent\SoftDeletes;


class Floor extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['floor_name', 'tower_id', 'wing_id',
    'status', 'created_by', 'updated_by'];

    function flat(){
        return $this->hasMany(Flat::class);
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
