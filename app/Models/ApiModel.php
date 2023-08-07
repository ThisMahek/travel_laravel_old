<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='users';
    protected $fillable = [
        'name', 'email', 'password','mobile','status','original_password'
    ];   
}
