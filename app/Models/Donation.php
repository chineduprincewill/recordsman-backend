<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'member_id', 'uid', 'fullname', 'mobile', 'email', 'gender', 'member_group', 'groupid', 'group', 'event', 'event_year', 'donation', 'redeemed', 'recorder', 'created_by', 'status'
    ];

}
