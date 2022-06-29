<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = ['sku', 'name', 'price', 'suitableWeather'];

    protected $casts = [
        'suitableWeather' => 'array'
    ];
}
