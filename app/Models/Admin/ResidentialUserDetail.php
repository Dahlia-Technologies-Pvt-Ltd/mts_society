<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Admin\{Floor,Tower,Wing,Flat};
use Illuminate\Database\Eloquent\SoftDeletes;


class ResidentialUserDetail extends Model
{
    use HasFactory;
    // use SoftDeletes;
    protected $fillable = [
        'societies_id', 'user_id', 'flat_id', 'parking_ids',
        'status','vehicle_types'
    ];
    protected $appends = ['vehicle_types_value'];
    public function getVehicleTypesValueAttribute($data)
    {
        if (!isset($this->attributes['vehicle_types']) || empty($this->attributes['vehicle_types'])) {
            return '';
        }
    
        $types = $this->attributes['vehicle_types'];
        $labels = config('util.vehicle_types');
    
        // Check if it's a single value or an array
        if (is_array($types)) {
            // Map each value to its corresponding label
            $mappedValues = array_map(function ($type) use ($labels) {
                return $labels[$type] ?? '';
            }, $types);
    
            return implode(', ', $mappedValues);
        } else {
            // Handle the case for a single value
            switch ($types) {
                case 2:
                    return $labels['2'] ?? '';
                case 4:
                    return $labels['4'] ?? '';
                default:
                    return $labels['0'] ?? '';
            }
        }
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
