<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $firstName = SUPER_ADMIN_FIRST_NAME ?? 'Super';
        $lastName = SUPER_ADMIN_LAST_NAME ?? 'Admin';
        $adminEmail = SUPER_ADMIN_EMAIL ?? 'cepochdevelopers@gmail.com';
        $adminObj = User::where('email', $adminEmail)->first();

        if (!$adminObj) {
            User::create([
                'name' =>  APP_NAME,
                'email' => $adminEmail,
                'password' => bcrypt('p04n8w{hV#:D$'),
                'user_type' => 'admin',
                'country_code' => '+91',
                'mobile' => '9988776655'
            ]);
        }
        User::create([
            'name' =>  $firstName .' '. $lastName,
            'first_name' =>  $firstName,
            'last_name' =>  $lastName,
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456'),
            'user_type' => 'admin',
            'country_code' => '+91',
            'mobile' => '9958568568'
        ]);

        $smtp = [
            'email' => 'cepochdevelopers@gmail.com',
            'password' => 'hmnnjrhhnzklcyae',
            'host' => 'smtp.gmail.com',
            'port' => '587',
            'from_address' => 'cepochdevelopers@gmail.com',
            'from_name' => APP_NAME,
        ];

        $jsonData = json_encode($smtp);

        $settingObj = Setting::where('name', 'smtp')->first();

        if (!$settingObj) {
            $settingObj = new Setting;
            $settingObj->name = 'smtp';
            $settingObj->description = 'SMTP setting is using to setup the mail configuration';
        }
        $settingObj->value = $jsonData;
        $settingObj->save();
    }
}
