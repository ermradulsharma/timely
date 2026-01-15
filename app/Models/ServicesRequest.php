<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicesRequest extends Model
{
    use HasFactory;

     public function customerDetails()
    {
        return $this->belongsTo('App\Models\User'::class, 'customer_id');
    }
     public function servicesProviderDetails()
    {
        return $this->belongsTo('App\Models\User'::class, 'services_provider_id');
    }

    public function services()
    {
        return $this->belongsTo('App\Models\Services'::class, 'service_id');
    }

}
