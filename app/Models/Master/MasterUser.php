<?php

namespace App\Models\Master;

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
        'name', 'username', 'user_code', 'email', 'password', 'phone_number',
        'master_society_id', 'gender', 'towerid', 'wingid', 'floorid',
        'flatid', 'street_address', 'country', 'state', 'city',
        'zipcode', 'profile_picture', 'status', 'created_by', 'updated_by',
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

    public function generateUserCode()
    {

        $index_assigned = $this->getCurrentMonthCount();

        switch (strlen($index_assigned)) {
            case 1:
                $new_index_assigned = "000" . $index_assigned;
                break;
            case 2:
                $new_index_assigned = "00" . $index_assigned;
                break;
            case 3:
                $new_index_assigned = "0" . $index_assigned;
                break;

            default:
                $new_index_assigned = $index_assigned;
        }
        $date = date("y");
        $format = "USER" . $date . $new_index_assigned;
        return $format;
    }
    public function getCurrentMonthCount()
    {
        $count = self::whereRaw('datepart(yyyy,created_at) = year(getdate())')->count();
        return $count > 0 ? $count + 1 : 1;
    }
}
