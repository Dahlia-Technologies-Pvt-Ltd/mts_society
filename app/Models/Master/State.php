<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\{MasterUser};

class State extends Model
{
    use HasFactory;
    protected $table = 'states';
    public function masteruser(){
        return $this->hasMany(MasterUser::class);
    }
}
