<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'master_user_id',
        'name',
        'user_code',
        'username',
        'email',
        'phone_number',
        'master_society_ids',
        'gender',
        'address',
        'country_id',
        'state_id',
        'city',
        'zipcode',
        'usertype',
        'status',
        'blocked_at',
        'profile_picture',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['deleted_at'];
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
        $format = "RESUSR" . $date . $new_index_assigned;
        return $format;
    }
    public function getCurrentMonthCount()
    {
        $count = self::whereRaw('datepart(yyyy,created_at) = year(getdate())')->count();
        return $count > 0 ? $count + 1 : 1;
    }
}