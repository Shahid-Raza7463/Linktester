<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    use HasFactory;
    protected $table = "country_proxies";
    protected $fillable = [
        'id',
        'iso',
        'ipAddress'
    ];
}
