<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterSubscription extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'master_subscriptions';
    protected $connection = 'sqlsrvmaster';
    protected $fillable = ['subscription_plan', 'price', 'frequency',
     'features', 'is_renewal_plan',
    'status', 'created_by', 'updated_by'];

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
    public function getIsRenewalPlanAttribute($data)
    {
        if (!isset($this->attributes['is_renewal_plan'])) {
            return '';
        }
        return $this->attributes['is_renewal_plan'] == 1 ? 'Renewed' : 'Not renewed';
    }
    
}
