<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkReport extends Model
{
    use HasFactory;
    protected $table = "links_report";
    protected $fillable = [
        'id',
        'url_id',
        'response',
        'status_code',
        'network_id'
    ];
}
