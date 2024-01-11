<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class MasterUser extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, HasApiTokens;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'phone_number',
        'master_society_id', 'gender', 'towerid', 'wingid', 'floorid',
        'flatid', 'street_address', 'country', 'state', 'city',
        'zipcode', 'status', 'created_by', 'updated_by',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getCreatedAtAttribute($value)
    {
        return $this->formatDate($value);
    }

    public function getUpdatedAtAttribute($value)
    {
        return $this->formatDate($value);
    }

    protected function formatDate($value)
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value)->format(config('util.default_date_time_format'));
    }
}
