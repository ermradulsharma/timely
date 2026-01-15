<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('countries')->count() == 0) {

            DB::table('countries')->insert([

                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'AB',
                    'state_name' => 'Alberta'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'BC',
                    'state_name' => 'British Columbia'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'MB',
                    'state_name' => 'Manitoba'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'NB',
                    'state_name' => 'New Brunswick'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'NL',
                    'state_name' => 'Newfoundland and Labrador'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'NT',
                    'state_name' => 'Northwest Territories'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'NS',
                    'state_name' => 'Nova Scotia'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'NU',
                    'state_name' => 'Nunavut'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'ON',
                    'state_name' => 'Ontario'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'PE',
                    'state_name' => 'Prince Edward Island'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'QC',
                    'state_name' => 'Quebec'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'SK',
                    'state_name' => 'Saskatchewan'
                ],
                [
                    'country_code' => 'CA',
                    'country_name' => 'Canada',
                    'state_code' => 'YT',
                    'state_name' => 'Yukon'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'Q0',
                    'state_name' => 'Aguascalientes'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'Q1',
                    'state_name' => 'Baja California'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'Q2',
                    'state_name' => 'Baja California Norte'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'Q3',
                    'state_name' => 'Baja California Sur'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'Q4',
                    'state_name' => 'Campeche'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'Q5',
                    'state_name' => 'Chiapas'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'Q6',
                    'state_name' => 'Chihuahua'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'Q7',
                    'state_name' => 'Coahuila'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'Q8',
                    'state_name' => 'Colima'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'Q9',
                    'state_name' => 'Distrito Federal'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QA',
                    'state_name' => 'Durango'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QG',
                    'state_name' => 'Edo. De Mexico'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QB',
                    'state_name' => 'Guanajuato'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QD',
                    'state_name' => 'Guerrero'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QE',
                    'state_name' => 'Hidalgo'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QF',
                    'state_name' => 'Jalisco'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QH',
                    'state_name' => 'Michoacan'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QI',
                    'state_name' => 'Morelos'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QJ',
                    'state_name' => 'Nayarit'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QK',
                    'state_name' => 'Nuevo Leon'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QL',
                    'state_name' => 'Oaxaca'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QM',
                    'state_name' => 'Puebla'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QN',
                    'state_name' => 'Queretaro'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QO',
                    'state_name' => 'Quintana Roo'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QP',
                    'state_name' => 'San Luis Potosi'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QQ',
                    'state_name' => 'Sinaloa'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QR',
                    'state_name' => 'Sonora'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QS',
                    'state_name' => 'Tabasco'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QT',
                    'state_name' => 'Tamaulipas'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QU',
                    'state_name' => 'Tlaxcala'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QV',
                    'state_name' => 'Veracruz'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QW',
                    'state_name' => 'Yucatan'
                ],
                [
                    'country_code' => 'MX',
                    'country_name' => 'Mexico',
                    'state_code' => 'QX',
                    'state_name' => 'Zacatecas'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'AL',
                    'state_name' => 'Alabama'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'AK',
                    'state_name' => 'Alaska'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'AZ',
                    'state_name' => 'Arizona'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'AR',
                    'state_name' => 'Arkansas'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'CA',
                    'state_name' => 'California'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'CO',
                    'state_name' => 'Colorado'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'CT',
                    'state_name' => 'Connecticut'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'DE',
                    'state_name' => 'Delaware'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'FL',
                    'state_name' => 'Florida'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'GA',
                    'state_name' => 'Georgia'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'HI',
                    'state_name' => 'Hawaii'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'ID',
                    'state_name' => 'Idaho'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'IL',
                    'state_name' => 'Illinois'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'IN',
                    'state_name' => 'Indiana'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'IA',
                    'state_name' => 'Iowa'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'KS',
                    'state_name' => 'Kansas'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'KY',
                    'state_name' => 'Kentucky'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'LA',
                    'state_name' => 'Louisiana'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'ME',
                    'state_name' => 'Maine'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'MD',
                    'state_name' => 'Maryland'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'MA',
                    'state_name' => 'Massachusetts'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'MI',
                    'state_name' => 'Michigan'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'MN',
                    'state_name' => 'Minnesota'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'MS',
                    'state_name' => 'Mississippi'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'MO',
                    'state_name' => 'Missouri'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'MT',
                    'state_name' => 'Montana'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'NE',
                    'state_name' => 'Nebraska'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'NV',
                    'state_name' => 'Nevada'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'NH',
                    'state_name' => 'New Hampshire'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'NJ',
                    'state_name' => 'New Jersey'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'NM',
                    'state_name' => 'New Mexico'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'NY',
                    'state_name' => 'New York'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'NC',
                    'state_name' => 'North Carolina'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'ND',
                    'state_name' => 'North Dakota'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'OH',
                    'state_name' => 'Ohio'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'OK',
                    'state_name' => 'Oklahoma'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'OR',
                    'state_name' => 'Oregon'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'PA',
                    'state_name' => 'Pennsylvania'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'RI',
                    'state_name' => 'Rhode Island'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'SC',
                    'state_name' => 'South Carolina'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'SD',
                    'state_name' => 'South Dakota'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'TN',
                    'state_name' => 'Tennessee'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'TX',
                    'state_name' => 'Texas'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'UT',
                    'state_name' => 'Utah'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'VT',
                    'state_name' => 'Vermont'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'VA',
                    'state_name' => 'Virginia'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'WA',
                    'state_name' => 'Washington'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'WV',
                    'state_name' => 'West Virginia'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'WI',
                    'state_name' => 'Wisconsin'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'WY',
                    'state_name' => 'Wyoming'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'DC',
                    'state_name' => 'Washington DC'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'AA',
                    'state_name' => 'Armed Forces Americas'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'AE',
                    'state_name' => 'Armed Forces Europe'
                ],
                [
                    'country_code' => 'US',
                    'country_name' => 'USA',
                    'state_code' => 'AP',
                    'state_name' => 'Armed Forces Pacific'
                ],

            ]);
        } else {
            echo "\e[31mTable is not empty, therefore NOT ";
        }
    }
}
