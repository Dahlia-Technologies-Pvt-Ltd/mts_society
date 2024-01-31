<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'master_socities_id',
        'usertype',
        'full_name',
        'username',
        'password',
        'email',
        'phone_number',
        'gender',
        'towerid',
        'wingid',
        'floorid',
        'flatid',
        'image',
        'street_address',
        'country',
        'state',
        'city',
        'zipcode',
        'country_code',
        'is_approv',
        'multilogin',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $dates = ['deleted_at'];
}