<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin\{Floor,Tower,Wing,Flat,MasterServiceProvider};
use Illuminate\Database\Eloquent\SoftDeletes;
class NonResidentialUserDetail extends Model
{
   
      
     
    //   ,[email]
    //   ,[country_code]
    //   ,[phone_number]
    //   ,[street_address]
    //   ,[country]
    //   ,[state]
    //   ,[city]
    //   ,[zipcode]
    //   ,[aadhaar_no]
    //   ,[pan_no]
    //   ,[master_service_provider_ids]
    //   ,[management_company_name]
    //   ,[management_company_country_code]
    //   ,[management_company_phone_number]
    //   ,[team_type]
    //   ,[status]
    //   ,[created_by]
    //   ,[updated_by]
    //   ,[deleted_at]
    //   ,[created_at]
    //   ,[updated_at]
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'societies_id', 'user_id', 'full_name', 'email','country_code',
        'phone_number','street_address','country','state',
        'city','zipcode','aadhaar_no','pan_no',
        'master_service_provider_ids','management_company_name','management_company_country_code',
        'management_company_phone_number','team_type','assigned_tower_ids',
        'status'
    ];
    protected $appends = ['master_service_provider_value','assigned_tower_names_value'];
    public function getMasterServiceProviderValueAttribute($data)
    {
        if (!isset($this->attributes['master_service_provider_ids'])) {
            return '';
        }
        $providerIds = json_decode($this->attributes['master_service_provider_ids'], true);

        // Assuming you have a Tower model with 'id' and 'tower_name' columns
        $providerNames = MasterServiceProvider::whereIn('id', $providerIds)->pluck('name')->toArray();

        return $providerNames;
    }
    // tower_names_value
    public function getAssignedTowerNamesValueAttribute()
    {
        if (!isset($this->attributes['assigned_tower_ids'])) {
            return '';
        }
        $towerIds = json_decode($this->attributes['assigned_tower_ids'], true);

        // Assuming you have a Tower model with 'id' and 'tower_name' columns
        $towerNames = Tower::whereIn('id', $towerIds)->pluck('tower_name')->toArray();

        return $towerNames;
    }
    // public function getParkingTypeValueAttribute($data)
    // {
    //     if (!isset($this->attributes['parking_type'])) {
    //         return '';
    //     }else if($this->attributes['parking_type']==0){
    //         return 'Resident parking';

    //     } 
    //     else{
    //         return 'Visitor parking';
    //     }
    // }
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
