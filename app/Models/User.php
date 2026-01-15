<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['subscription_date', 'average_rating', 'review_count'];

    public static function checkUser($data)
    {
        $response = [];
        if (array_key_exists('username', $data)) {
            if (Auth::validate(array('username' => $data['username'], 'password' => $data['password']))) {
                $user = User::where('username', $data['username'])->first();
                $response['user'] = $user;
            } else {
                $response['message'] = 'Incorrect username or password';
            }
        } elseif (array_key_exists('email', $data)) {
            $userObj = User::where('email', strtolower($data['email']))->first();
            if (!$userObj) {
                $response['message'] = 'User does not exist';
                return $response;
            }
            if (Auth::validate(array('email' => strtolower($data['email']), 'password' => $data['password']))) {
                $user = User::where('email', strtolower($data['email']))->first();
                $response['user'] = $user;
            } else {
                $response['message'] = 'Please enter valid password';
            }
        } else {
            $response['message'] = 'Please enter valid email';
        }
        return $response;
    }

    public static function webLogin($data)
    {
        $response = [];
        if (array_key_exists('username', $data)) {
            if (Auth::attempt(array('username' => $data['username'], 'password' => $data['password']))) {
                $user = User::where('username', $data['username'])->first();
                $response['user'] = $user;
            } else {
                $response['message'] = 'Incorrect username or password';
            }
        } elseif (array_key_exists('email', $data)) {
            $userObj = User::where('email', strtolower($data['email']))->first();
            if (!$userObj) {
                $response['message'] = 'Incorrect email';
                return $response;
            }
            if (Auth::attempt(array('email' => strtolower($data['email']), 'password' => $data['password']))) {
                $user = User::where('email', strtolower($data['email']))->first();
                $response['user'] = $user;
            } else {
                $response['message'] = 'Incorrect password';
            }
        } else {
            $response['message'] = 'Email or username is required';
        }

        return $response;
    }

    public function getImageAttribute($value = '')
    {
        if (!empty($value)) {
            return asset('/uploads/images/' . $value);
        }
        return asset('/images/default-profile.jpg');
    }

    /*
    public function children()
    {
        return $this->hasMany(Children::class, 'user_id');
    }

    public function today_mood()
    {
        return $this->hasOne(Mood::class, 'children_id');
    }
    */

    public function providerCategory()
    {
        return $this->hasMany(ProviderCategory::class, 'provider_id');
    }
    public function services()
    {
        return $this->hasMany(ServiceProvider::class, 'provider_id')->with('category');
    }

    public function skills()
    {
        return $this->hasMany(UserSkill::class, 'user_id');
    }
    public function work_experiences()
    {
        return $this->hasMany(UserExperience::class, 'user_id')->orderBy('start_date', 'DESC');
    }

    public function educations()
    {
        return $this->hasMany(UserEducation::class, 'user_id')->orderBy('start_date', 'DESC');
    }

    public function work_experiencesNew()
    {
        return $this->hasMany(UserExperience::class, 'user_id')->orderBy('created_at', 'DESC');
    }

    public function educationsNew()
    {
        return $this->hasMany(UserEducation::class, 'user_id')->orderBy('created_at', 'DESC');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'employer_id');
    }

    public function getJobCountAttribute()
    {
        return $this->hasMany(Job::class, 'employer_id')->count();
    }

    public function getAppliedJobCountAttribute()
    {
        return $this->hasMany(AppliedJob::class, 'user_id')->count();
    }

    public function getFavouriteJobCountAttribute()
    {
        return $this->hasMany(FavouriteJob::class, 'user_id')->count();
    }

    public function getTotalAppliedCountAttribute()
    {
        $userId = Auth::user()->id ?? 0;

        $jobIds = Job::where('employer_id', $userId)->get()->pluck('id');

        return AppliedJob::whereIn('job_id', $jobIds)->count();
    }

    public function applied_jobs()
    {
        return $this->hasMany(AppliedJob::class, 'user_id');
    }

    public function getResumeAttribute($value)
    {
        $userId = $this->id;

        if (!empty($value) && $this->user_type == 'user') {
            $userObj = User::where('id', $userId)->with('skills', 'skills.skill_detail:id,title')->first();

            $workExperience = UserExperience::where('user_id', $userId)->orderBy('start_date', 'DESC')->get();
            $userObj['work_experiences'] = $workExperience;

            $workExperience = UserEducation::where('user_id', $userId)->orderBy('start_date', 'DESC')->get();
            $userObj['educations'] = $workExperience;

            $path = public_path() . '/resume';
            File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

            /* $fileCheck = public_path() . '/resume/' . $userId . "XXXX.pdf";

            if (!is_file($fileCheck)) {
                $contents = '';
                //shell_exec('sudo chmod 0777 '. $fileCheck);
                file_put_contents($fileCheck, $contents);
            } */

            $html = view('resume')->with(compact('userObj'))->render();

            $pdf = Pdf::loadHtml($html);
            /** @var Response $response */

            $fileName = $userId . ".pdf";

            $result = $pdf->save(public_path('resume/' . $fileName));

            return asset('resume/' . $value);
        }

        return NULL;
    }

    public function getIsProfileUpdatedAttribute($value)
    {
        return $value == '1' ? TRUE : FALSE;
    }

    public function subscription()
    {
        return $this->hasOne('App\Models\Subscription', 'user_id');
    }

    public function getSubscriptionDateAttribute()
    {
        $userId = $this->id;

        $subscription = Subscription::where('user_id', $userId)->latest()->first();

        if ($subscription) {
            return $subscription->subscription_date;
        }

        return NULL;
    }

    public function service_provider()
    {
        return $this->hasMany(ServiceProvider::class, 'provider_id')->with('servicedetails', 'category');
    }
    public function servicedetails()
    {
        return $this->hasMany(Services::class, 'service_id');
    }

    public function getAverageRatingAttribute()
    {
        $userId = $this->id;

        $averageRating = Rating::where('rating_send_to', $userId)->get()->avg('rating') ?? 0;

        return number_format((float)$averageRating, 1, '.', '');
    }

    public function getReviewCountAttribute()
    {
        $userId = $this->id;

        return Rating::where('rating_send_to', $userId)->count();
    }

    public function getReviewAttribute()
    {
        $userId = $this->id;

        return Rating::where('rating_send_to', $userId)->get();
    }
}
