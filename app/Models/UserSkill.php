<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    use HasFactory;

    public function skill_detail()
    {
        return $this->belongsTo(Skill::class, 'skill_id');
    }
}
