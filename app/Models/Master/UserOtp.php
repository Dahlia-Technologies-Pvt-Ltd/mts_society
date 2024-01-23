<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOtp extends Model
{
    use HasFactory;
    // use SoftDeletes;
    protected $table = 'user_otps';
    protected $fillable = ['master_user_id', 'otp'
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
    

    public function getExpireAtAttribute($data)
    {
        if (!isset($this->attributes['expire_at'])) {
            return '';
        }
        return Carbon::parse($this->attributes['expire_at'])->format(config('util.default_date_time_format'));
    }
}
