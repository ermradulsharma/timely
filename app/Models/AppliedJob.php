<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppliedJob extends Model
{
    use HasFactory;

    public function job_detail()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function user_detail()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
