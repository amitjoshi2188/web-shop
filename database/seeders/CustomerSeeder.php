<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Customer::truncate(); // removes existing data before inserting
        $csvData = fopen(base_path('database/csv/customers.csv'), 'r');
        $transRow = true;
        while (($data = fgetcsv($csvData, 555, ',')) !== false) {
            if (!$transRow) {
                //converting date format to store it in database.
                $currentString = explode(',', $data[6]);
                $newDateFormat = $currentString[1] . ',' . $currentString[2];

                Customer::create([
                    'job_title' => $data['1'],
                    'email' => $data['2'],
                    'name' => $data['3'],
                    'first_name' => $data['4'],
                    'last_name' => $data['5'],
                    'registered_since' => date("Y-m-d", strtotime($newDateFormat)),
                    'phone' => $data['7'],
                ]);
            }
            $transRow = false;
        }
        fclose($csvData);
    }
}
