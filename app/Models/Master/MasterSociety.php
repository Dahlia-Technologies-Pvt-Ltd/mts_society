<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use App\Models\Master\MasterDatabase;

class MasterSociety extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'master_socities';
    protected $fillable = ['society_unique_code', 'society_name',
     'email', 'phone_number',
    'address','adress2','country_id','state_id','city','zipcode','gst_number','pan_number',
    'status','created_by', 'updated_by'];
    protected $appends =['society_token'];
    
    function Databases(){
        return $this->hasOne(MasterDatabase::class, 'master_socities_id', 'id');
    }

    public function getSocietyTokenAttribute()
    {
        return Crypt::encryptString($this->id);
       
    }
    public function getDocumentsAttribute($data)
    {
        if (!isset($this->attributes['documents'])) {
            return null;
        }
        if ($this->attributes['documents'] === null) {
            return null;
        }
        return asset('storage') . '/' . $this->attributes['documents'];
        // return asset('storage').'/app/' . $this->attributes['attachments'];
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

    public function generateSocietyCode()
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
        $format = "SOC" . $date . $new_index_assigned;
        return $format;
    }
    public function getCurrentMonthCount()
    {
        $count = self::whereRaw('datepart(yyyy,created_at) = year(getdate())')->count();
        return $count > 0 ? $count + 1 : 1;
    }
}
