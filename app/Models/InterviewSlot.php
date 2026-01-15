<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewSlot extends Model
{
    use HasFactory;

    protected $appends = ['title', 'description', 'color', 'booking_slot_time'];

    public function getTitleAttribute()
    {
        $startTime = date("h:i A", strtotime($this->start_time));
        $endTime = date("h:i A", strtotime($this->end_time));

        return $startTime . " - " . $endTime . " (" . (isset($this->user_id) && !empty($this->user_id) ? 'Booked' : 'Available') . ")";
    }

    public function getDescriptionAttribute()
    {
        $startTime = date("h:i A", strtotime($this->start_time));
        $endTime = date("h:i A", strtotime($this->end_time));

        return $startTime . " - " . $endTime . " (" . (isset($this->user_id) && !empty($this->user_id) ? 'Booked' : 'Available') . ")";
    }

    public function getStartTimeAttribute($value = "")
    {
        return date("H:i", strtotime($value));
    }

    public function getEndTimeAttribute($value = "")
    {
        return date("H:i", strtotime($value));
    }

    public function getColorAttribute($value = "")
    {
        if (isset($this->user_id) && !empty($this->user_id)) {
            return "red";
        }
        return 'green';
    }

    public function user_detail()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getBookingTimeAttribute($value = "")
    {
        if (isset($value) && !empty($value)) {
            return date("d F, Y h:i A", strtotime($value));
        }
        return '';
    }

    public function getBookingSlotTimeAttribute()
    {
        $startTime = date("h:i A", strtotime($this->start_time));
        $endTime = date("h:i A", strtotime($this->end_time));

        return $startTime . " - " . $endTime;
    }
}
