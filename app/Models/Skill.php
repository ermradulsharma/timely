<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    public function getIsAddedAttribute()
    {
        $userId = \Auth::user()->id ?? 0;

        if (UserSkill::where(['user_id' => $userId, 'skill_id' => $this->id])->first()) {
            return TRUE;
        }
        return FALSE;
    }
}
