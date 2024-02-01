<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\{MasterUser};

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';
    protected $connection = 'sqlsrvmaster';
    public function masteruser(){
        return $this->hasMany(MasterUser::class);
    }
}
