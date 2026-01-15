<?php

namespace Database\Seeders;

use App\Models\JobExperience;
use Illuminate\Database\Seeder;

class JobExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'title' => '0-1 Year',
                'min' => 0,
                'max' => 1,
            ],
            [
                'title' => '1-3 Year',
                'min' => 1,
                'max' => 3,
            ],
            [
                'title' => '3-5 Year',
                'min' => 3,
                'max' => 5,
            ],
            [
                'title' => '5-10 Year',
                'min' => 5,
                'max' => 10,
            ],
            [
                'title' => '10+ Year',
                'min' => 10,
                'max' => 100,
            ],
        ];

        foreach($data as $key => $value) {
            $jobExperienceObj = JobExperience::where('title', $value['title'])->first();

            if(!$jobExperienceObj) {
                $jobExperienceObj = new JobExperience;
                $jobExperienceObj->title = $value['title'];
                $jobExperienceObj->min = $value['min'];
                $jobExperienceObj->max = $value['max'];
                $jobExperienceObj->save();
            }
        }
    }
}
