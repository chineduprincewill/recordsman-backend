<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category', 'uid', 'lastname', 'firstname', 'othernames', 'fullname', 'mobile', 'email', 'gender', 'branchid', 'branch', 'wing', 'email', 'created_by', 'status'
    ];
    
}
