<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinksTester extends Model
{
    use HasFactory;
    protected $table = "links_tester";
    protected $fillable = [
        'id',
        'url',
        'country',
        'device',
        'ip_address',
        'user_agent',
        'network_id'
    ];
}
