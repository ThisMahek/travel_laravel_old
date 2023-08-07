<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='tbl_otp';
    protected $fillable=[
        'status','email_otp','mobile_otp','email','mobile'
    ];
}
