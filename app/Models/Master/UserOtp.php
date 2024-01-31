<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Mail;
use Illuminate\Support\Str;
use App\Helpers\MailHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOtp extends Model
{
    use HasFactory;
    // use SoftDeletes;
    protected $table = 'user_otps';
    protected $fillable = [
        'master_user_id', 'otp', 'expire_at', 'created_by', 'updated_by'
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

    static public function getTokenSingle($Token)
    {
        return MasterUser::where('forgot_password_token', '=', $Token)->where('forgot_password_token_time', '>=', Carbon::now())->first();
    }

    public function sendotp($params = [])
    {
        $random_otp = rand(100000, 999999);
        $ins_arr = ['otp' => $random_otp, 'expire_at' => Carbon::now()->addMinutes(3)];
        $qry = UserOtp::updateOrCreate(
            ['master_user_id' => $params['id']],
            $ins_arr
        );
        try {
            $TemplateData = array(
                'EMAIL' => $params['email'],
                'USER_NAME' => $params['name'],
                'OTP' => $random_otp
            );
            MailHelper::sendMail('SEND_OTP', $TemplateData);
            $response['status'] = 200;
            $response['message'] = 'OTP sent successfully';
        } catch (Exception $exp) {
            $response['status'] = 503;
            $response['message'] = 'Oops ! We are unable to send mail , please try again after sometime.';
        }
        return $response;
    }

    public function verifyOtp($params = [])
    {
        $otp = isset($params['otp']) ? $params['otp'] : -1;
        $keyword = isset($params['keyword']) ? $params['keyword'] : -1; 
        if($otp == -1 || $keyword == -1){
            return 0;
        }
        $data_query = UserOtp::join('master_users', 'master_users.id', '=', 'user_otps.master_user_id')->where('otp', $otp)->where('expire_at', '>=', Carbon::now());
        $data_query->where(function ($query) use ($keyword) {
            $query->where('master_users.phone_number', '=', $keyword)
                ->orWhere('master_users.email', '=', $keyword);
        });
        if ($data_query->exists()) {
            $data_query->select([
                'master_users.id', 'user_otps.id AS otp_id'
            ]);
            $user_otp_data = $data_query->first();

            $user_otp = UserOtp::find($user_otp_data->otp_id);
            $user_otp->otp = 0;
            $user_otp->expire_at = Carbon::now()->subMinutes(5);
            $user_otp->save();
            return $user_otp_data->id;
        } else {
            return 0;
        }
    }
}
