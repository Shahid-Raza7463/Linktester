<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryName extends Model
{
    use HasFactory;
    protected $table = "country";
    protected $fillable = [
        'id',
        'country',
        'iso_code'
    ];
}
