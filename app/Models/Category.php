<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    use HasFactory;

    protected $appends = ['price', 'is_selected'];
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

    public function categoryPrice()
    {
        return $this->hasMany(ServiceProvider::class, 'category_id')->select(array('id', 'category_id', 'price'));
    }

    public function getPriceAttribute($value)
    {
        $userId = Auth::user()->id;
        $ServiceProvider = ServiceProvider::where(['category_id' => $this->id, 'provider_id' => $userId])->first();
        if ($ServiceProvider) {
            return (float)$ServiceProvider->price;
        }
        return  0.00;
    }

    public function getIsSelectedAttribute($value)
    {
        $userId = Auth::user()->id;
        $ServiceProvider = ServiceProvider::where(['category_id' => $this->id, 'provider_id' => $userId])->first();
        if ($ServiceProvider) {
            return true;
        }
        return false;
    }
}
