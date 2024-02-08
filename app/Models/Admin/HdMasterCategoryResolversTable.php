<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin\{MasterServiceProvider};

class HdMasterCategoryResolversTable extends Model
{
    use HasFactory;
    protected $table = 'hd_master_category_resolvers_table';
    protected $fillable = [
        'hd_master_category_id', 'hd_master_sub_category_id',
         'master_service_provider_ids', 'user_id'
    ];
    protected $appends = ['master_service_provider_value'];
    public function getMasterServiceProviderValueAttribute($data)
    {
        if (!isset($this->attributes['master_service_provider_ids'])) {
            return '';
        }
        $providerIds = json_decode($this->attributes['master_service_provider_ids'], true);
        $providerNames = MasterServiceProvider::whereIn('id', $providerIds)->pluck('name')->toArray();

        return $providerNames;
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
}
