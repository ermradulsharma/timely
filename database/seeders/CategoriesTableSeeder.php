<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $categories = [
            [
                'name' => 'Change Tire',
            ],
            [
                'name' => 'Gas Filing’s  Station',
            ],
            [
                'name' => 'Jump Start',
            ],
            [
                'name' => 'Towing Cars',
            ],
            [
                'name' => 'Battery Charging',
            ]
        ];

        Category::insert($categories);
    }
}
