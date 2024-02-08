<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin\{Floor,Tower,Wing,Flat};
use Illuminate\Database\Eloquent\SoftDeletes;

class HdMasterSubCategories extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'hd_master_category_id', 'sub_category_name', 'image'
    ];
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
