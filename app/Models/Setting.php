<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /* public function getSettingsAttribute($value = "")
    {
        if (!empty($value)) {
            return json_decode($value, TRUE);
        }

        return [];
    } */

    public function getValueAttribute($value = "")
    {
        if (!empty($value)) {
            return json_decode($value, TRUE);
        }

        return [];
    }
}
