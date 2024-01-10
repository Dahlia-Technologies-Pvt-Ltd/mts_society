<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class MasterUser extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'master_users';
    protected $fillable = ['name', 'username', 'email', 'password', 'phone_number',
    'master_society_id', 'gender', 'towerid', 'wingid', 'floorid',
    'flatid', 'street_address', 'country', 'state', 'city',
    'zipcode', 'status', 'created_by', 'updated_by'];

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