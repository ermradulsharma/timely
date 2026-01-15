<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
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
                'title' => 'Problem-solving',
            ],
            [
                'title' => 'Time management ',
            ],
            [
                'title' => 'Critical thinking',
            ],
            [
                'title' => 'Decision-making ',
            ],
            [
                'title' => 'Organizational',
            ],
            [
                'title' => 'Stress Management',
            ],
            [
                'title' => 'Communication',
            ],
            [
                'title' => 'Teamwork',
            ],
        ];

        foreach ($data as $key => $value) {
            $skillObj = Skill::where('title', $value['title'])->first();

            if (!$skillObj) {
                $skillObj = new Skill;
                $skillObj->title = $value['title'];
                $skillObj->save();
            }
        }
    }
}
