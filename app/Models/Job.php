<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $appends = [
        'contract_type_detail', 'employment_type_detail'
    ];

    public function experience_detail()
    {
        return $this->belongsTo(JobExperience::class, 'job_experience_id');
    }

    public function getJobStatusAttribute()
    {
        $userId = \Auth::user()->id ?? 0;

        $appliedJobObj = AppliedJob::where(['user_id' => $userId, 'job_id' => $this->id])->first();

        if (!$appliedJobObj) {
            return '0';
        }

        return $appliedJobObj->status;
    }

    public function applied()
    {
        return $this->hasMany(AppliedJob::class, 'job_id');
    }

    public function applied_job_detail()
    {
        $userId = \Auth::user()->id ?? 0;

        return $this->hasOne(AppliedJob::class, 'job_id')->where('user_id', $userId);
    }

    public function favourite_detail()
    {
        $userId = \Auth::user()->id ?? 0;

        return $this->hasOne(FavouriteJob::class, 'job_id')->where('user_id', $userId);
    }

    public function getIsFavouriteAttribute()
    {
        $userId = \Auth::user()->id ?? 0;

        if (FavouriteJob::where(['user_id' => $userId, 'job_id' => $this->id])->first()) {
            return TRUE;
        }

        return FALSE;
    }

    public function getTotalAppliedCountAttribute()
    {
        return AppliedJob::where('job_id', $this->id)->count();
    }

    public function getInterviewingCountAttribute()
    {
        return AppliedJob::where('job_id', $this->id)->where('status', '2')->count();
    }

    public function getHiredCountAttribute()
    {
        return AppliedJob::where('job_id', $this->id)->where('status', '3')->count();
    }

    public function applied_status()
    {
        return $this->hasOne(AppliedJob::class, 'job_id');
    }

    public function getContractTypeDetailAttribute()
    {
        return ContractType::where('id', $this->contract_type_id ?? 0)->withTrashed()->first();
    }

    public function getEmploymentTypeDetailAttribute()
    {
        return EmploymentType::where('id', $this->employment_type_id ?? 0)->withTrashed()->first();
    }

    public function getContractTypeAttribute()
    {
        $dataObj = ContractType::where('id', $this->contract_type_id ?? 0)->withTrashed()->first();

        return $dataObj->title ?? "";
    }

    public function getTypeOfEmploymentAttribute()
    {
        $dataObj = EmploymentType::where('id', $this->employment_type_id ?? 0)->withTrashed()->first();

        return $dataObj->title ?? "";
    }
}
