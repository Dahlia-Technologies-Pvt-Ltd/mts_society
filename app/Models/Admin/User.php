<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'master_user_id',
        'name',
        'user_code',
        'username',
        'email',
        'phone_number',
        'master_society_ids',
        'gender',
        'address',
        'country_id',
        'state_id',
        'city',
        'zipcode',
        'usertype',
        'status',
        'blocked_at',
        'profile_picture',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['deleted_at'];
}