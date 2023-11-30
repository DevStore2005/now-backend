<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'description',
    ];
}
