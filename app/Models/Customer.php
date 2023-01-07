<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_title',
        'email',
        'name',
        'first_name',
        'last_name',
        'registered_since',
        'phone'
    ];

    protected $dates = ['registered_since']; // define to access by carbon for any date related operations.

}
