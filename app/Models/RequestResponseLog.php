<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestResponseLog extends Model
{
    use HasFactory;

    protected $casts = [
        'request_params' => 'array',
        'response'       => 'array',
        'extra'          => 'array',
    ];
}
