<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_send_to');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id_send_by');
    }

    public function serviceBooking()
    {
        return $this->hasMany(ServicesBooking::class, 'booking_id');
    }

    public function customerDetails()
    {
        return $this->belongsTo(User::class, 'user_id_send_by');
    }

    public function servicesProviderDetails()
    {
        return $this->belongsTo(User::class, 'provider_send_to');
    }

    public function paymentRequest()
    {
        return $this->hasOne(PaymentRequest::class, 'booking_id');
    }
}
