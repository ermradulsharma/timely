<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderService extends Model
{
    use HasFactory;

    public function services()
    {
        return $this->hasMany(services::class, 'services_id');
    }
    public function User()
    {
        return $this->hasMany(User::class, 'user');
    }
}
