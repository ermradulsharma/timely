<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;
    
    public function rejectRequest()
    {
        return $this->hasMany(RejectPayment::class, 'paymentReq_id');
    }

    public function AdditionalCost(){
        return $this->hasMany(AdditionalCost::class, 'payment_req_id');
    }
}
