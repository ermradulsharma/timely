<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public function user_detail()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service_provider()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer_details()
    {
        return $this->belongsTo(User::class, 'payment_by');
    }


    public function payment_by_detail()
    {
        return $this->belongsTo(User::class, 'payment_by');
    }

    public function job_detail()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}
