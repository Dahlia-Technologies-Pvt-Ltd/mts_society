<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin\{Tower,Parking};

class Wing extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['wings_name', 'tower_id',
    'status', 'created_by'];

    function tower(){
        return $this->belongsTo(Tower::class);
    }
    function parking()
    {
        return $this->hasMany(Parking::class);
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
