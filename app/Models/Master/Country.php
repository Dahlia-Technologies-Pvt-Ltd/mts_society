<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\{MasterUser};

class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';
    protected $connection = 'sqlsrvmaster';
    public function masteruser(){
        return $this->hasMany(MasterUser::class);
    }
}
