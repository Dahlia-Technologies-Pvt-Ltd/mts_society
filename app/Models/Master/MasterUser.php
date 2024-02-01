<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Master\{Country,State,City,MasterSociety};

class MasterUser extends Authenticatable
{
    use HasFactory, SoftDeletes, Notifiable, HasApiTokens;
    protected $connection = 'sqlsrvmaster';
    protected $fillable = [
        'name', 'username', 'user_code', 'email', 'password', 'phone_number',
        'master_society_ids', 'gender', 'address', 'country_id', 'state_id', 'city',
        'zipcode', 'usertype', 'blocked_at', 'forgot_password_token','forgot_password_token_time',
        'profile_picture', 'status', 'created_by', 'updated_by'
    ];
    protected $appends = ['societies'];

    function getSocietiesAttribute()
    {
        if (!isset($this->attributes['master_society_ids'])) {
            return [];
        }
        $master_society_ids = json_decode($this->attributes['master_society_ids']);
        $ms_obj = MasterSociety::whereIn('id',$master_society_ids)->where('status',0);
        if($ms_obj->exists()){
            return $ms_obj->select(['id','society_name'])->get()->makeHidden('society_owner');
        }
        return [];
    }

    static public function getEmailSingle($Email)
    {
        return MasterUser::where('email', '=', $Email)->where('status',0)->first();
    }
    static public function getTokenSingle($Token)
    {
        return MasterUser::where('forgot_password_token', '=', $Token)->where('forgot_password_token_time', '>=', Carbon::now())->first();
    }
    function country(){
        return $this->belongsTo(Country::class);
    }
    function state(){
        return $this->belongsTo(State::class);
    }
    function city(){
        return $this->belongsTo(City::class);
    }
    public function getProfilePictureAttribute($data)
    {
        if (!isset($this->attributes['profile_picture'])) {
            return '';
        }

        $default = asset('storage') . '/uploads/user_profile_pic/noimage.png';
        if ($this->attributes['profile_picture'] === null) {
            //$images[] = $default; return $images;
            //After Discussion with wen team, they want null here instead of no image so that on edit page it will not show "noimage" image by default if images does not exists
            return null;            
        }
        
        // $images = [];
        // foreach (json_decode($this->attributes['profile_picture']) as $image) {
            $filename = asset('storage') . '/' . $this->attributes['profile_picture'];
            if(Storage::exists($this->attributes['profile_picture'])){
                $images[] = $filename;
            }
        // }
        //print_r($images); die;
        if (empty($images)) {
            $images = $default;
        }
        return $images;
    }

    // public function getUserTypeAttribute($data)
    // {
    //     if (!isset($this->attributes['usertype'])) {
    //         return '';
    //     }
    //     $usertype = $this->attributes['usertype'];

    //     switch ($usertype) {
    //         case 1:
    //             return 'Admin';
    //         case 2:
    //             return 'Super Admin';
    //         case 3:
    //             return 'Other User Type';
    //         default:
    //             return 'Unknown User Type';
    //     }
    // }

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
