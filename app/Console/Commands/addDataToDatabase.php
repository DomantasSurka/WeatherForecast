<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Product;

class addDataToDatabase extends Command
{
    protected $signature = 'db:addData';

    protected $description = 'Adding data to database';

    public function handle()
    {
        $json = File::get("database/data/defaultData.json");

        $data = json_decode($json);

        foreach($data as $item) {
            Product::insert(
            [
                'sku'               => $item->sku,
                'name'              => $item->name,
                'price'             => $item->price,
                'suitableWeather'   => $item->suitableWeather,
            ]
            );
        }
    }
}
