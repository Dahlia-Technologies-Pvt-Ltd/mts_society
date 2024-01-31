<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSubscription extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $connection = 'sqlsrvmaster';
    protected $fillable = ['master_subscription_id', 'master_user_id', 'master_socities_id', 'subscription_plan', 'price', 'frequency',
    'features', 'is_renewal_plan', 'status', 'created_by', 'updated_by'];

    public function getCreatedAtAttribute($data)
    {
        if (!isset($this->attributes['created_at'])) {
            return '';
        }
        return Carbon::parse($this->attributes['created_at'])->format(config('util.default_date_time_format'));
    }
}
