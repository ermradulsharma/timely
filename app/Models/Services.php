<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Services extends Model
{
    use HasFactory, SoftDeletes;

    public function getImageAttribute($value = "")
    {
        return asset('uploads/services/' . $value);
    }

    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->timezone($this->timezone ?? 'UTC')->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->timezone($this->timezone ?? 'UTC')->toDateTimeString();
    }

    public function servicesname()
    {
        return $this->belongsTo(Services::class, 'id', 'service_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
