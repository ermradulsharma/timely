<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $appends = [
        'averageRating',
        'review_count',
        'name'
    ];

    public function servicedetails()
    {
        return $this->belongsTo(Services::class, 'service_id')->with('category') ?? [];
    }

    public function userdetails()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function getAverageRatingAttribute()
    {
        $userId = $this->provider_id ?? 0;

        $averageRating = Rating::where('rating_send_by', $userId)->get()->avg('rating') ?? 0;

        return number_format((float)$averageRating, 1, '.', '');
    }

    public function getReviewCountAttribute()
    {
        $userId = $this->provider_id ?? 0;

        return Rating::where('rating_send_by', $userId)->count();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getNameAttribute()
    {
        $categoryId = $this->category_id;
        $category = Category::find($categoryId);
        if ($category) {
            return $category->name ?? "";
        }
    }
}
