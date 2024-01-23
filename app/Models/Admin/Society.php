<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Society extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'master_society_id',
        'society_unique_code',
        'society_name',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['deleted_at'];
}