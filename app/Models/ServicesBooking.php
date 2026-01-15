<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicesBooking extends Model
{
    use HasFactory;
    public function service()
    {
        return $this->belongsTo(Category::class,'service_id');
    }
}
