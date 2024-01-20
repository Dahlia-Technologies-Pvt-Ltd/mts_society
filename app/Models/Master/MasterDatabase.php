<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\MasterSociety;
use Carbon\Carbon;
class MasterDatabase extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'master_database';
    protected $connection = 'sqlsrv';
    protected $fillable = ['databasename', 'databaseuid', 'databasepwd', 'master_user_id', 'master_socities_id',
    'status', 'created_by', 'updated_by'];

    public function getCreatedAtAttribute($data)
    {
        if (!isset($this->attributes['created_at'])) {
            return '';
        }
        return Carbon::parse($this->attributes['created_at'])->format(config('util.default_date_time_format'));
    }

    public function MasterSociety(){
        return $this->belongsTo(MasterSociety::class, 'master_socities_id', 'id');
    }

    public function getUpdatedAtAttribute($data)
    {
        if (!isset($this->attributes['updated_at'])) {
            return '';
        }
        return Carbon::parse($this->attributes['updated_at'])->format(config('util.default_date_time_format'));
    }
}
