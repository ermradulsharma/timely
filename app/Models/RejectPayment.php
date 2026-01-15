<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectPayment extends Model
{
    use HasFactory;

    public function payment(){
        return $this->belongsTo(PaymentRequest::class, 'id', 'paymentReq_id');
    }
}
