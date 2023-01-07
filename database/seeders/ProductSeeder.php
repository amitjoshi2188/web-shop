<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::truncate(); // removes existing data before inserting
        $csvData = fopen(base_path('database/csv/products.csv'), 'r');
        $transRow = true;
        while (($data = fgetcsv($csvData, 555, ',')) !== false) {
            if (!$transRow) {

                Product::create([
                    'name' => $data['1'],
                    'price' => $data['2']
                ]);
            }
            $transRow = false;
        }
        fclose($csvData);
    }
}
