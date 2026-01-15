<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewVideo extends Model
{
    use HasFactory, SoftDeletes;

    public function getFileAttribute($value = '')
    {
        if (!empty($value)) {
            return asset('uploads/interview_question/' . $value);
        }
        return $value;
    }

    public function questions()
    {
        return $this->hasMany(InterviewVideoQuestion::class, 'interview_video_id');
    }
}
