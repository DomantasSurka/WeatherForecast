<?php
declare(strict_types=1);
namespace App\Services;

use App\Http\Controllers\WeatherProductController;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    /**
     * Main function to get products recommendations according to specified city's weather.
     * Formatting .json array output of recommendations.
     * @param string $city requested city
     */
    public function getWeatherRecommendationsForCity(string $city)
    {
        $weatherJson = (new WeatherProductController())->requestAndGetWeatherData($city);

        $result["city"] = $city;
        $today = date("Y-m-d");
        $recommendationsCount = 3;

        for($i = 0; $i < $recommendationsCount; $i++){
            $day = date('Y-m-d', strtotime($today . (' +' . ($i + 1)) . ' day'));
            $weatherType = $this->getMostOccurringWeatherType($weatherJson['forecastTimestamps'], $day);
            $suitableProducts = $this->getProductsForWeather($weatherType);

            $weather["weather_forecast"] = $weatherType;
            $weather["date"] = $day;
            if(empty($suitableProducts)){
                throw new \InvalidArgumentException("No recommended products available.");
            } else if(count($suitableProducts["products"]) == 1){
                $weather["products"][0] = $suitableProducts["products"][0];
            } else{
                $weather["products"][0] = $suitableProducts["products"][rand(0, (int)((count($suitableProducts["products"])-1)/2))];
                $weather["products"][1] = $suitableProducts["products"][rand((int)((count($suitableProducts['products'])-1)/2+1), count($suitableProducts['products'])-1)];
            }
            $result["recommendations"][] = $weather;
        }
        return json_encode($result, JSON_PRETTY_PRINT);
    }

    /**
     * Function which filters products who are suitable for specified weather type.
     * @param string $weatherType weather forecast
     * @return array suitable products array.
     */
    private function getProductsForWeather(string $weatherType): array
    {
        $productsData = $this->getProductsDataFromDatabase();
        $result = array();

        foreach($productsData as $product){
            $suitableWeathers = $product -> suitableWeather;
            foreach($suitableWeathers as $suitableWeather){
                if($suitableWeather == $weatherType){
                    $products["sku"] = $product->sku;
                    $products["name"] = $product->name;
                    $products["price"] = $product->price;
                    $result["products"][] = $products;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Function which gets most occurring weather at the specified day.
     * @param array $weatherData weather forecasts
     * @param string $day day to filter by
     * @return string weather type
     */
    private function getMostOccurringWeatherType(array $weatherData, string $day): string
    {
        $weathersAtSpecifiedDay = $this->getWeathersForSpecifiedDate($weatherData, $day);

        $descendingOccurrences = array_count_values($weathersAtSpecifiedDay);

        arsort($descendingOccurrences);

        $mostOccurringWeather = array_keys($descendingOccurrences);

        return array_shift($mostOccurringWeather);
    }

    /**
     * Function which gets weather types array at the specified day.
     * Returns array of weathers.
     * @param array $weatherData weather forecasts
     * @param string $day day to filter by
     * @return array weathers at specified day
     */
    private function getWeathersForSpecifiedDate(array $weatherData, string $day): array
    {
        $weathersAtSpecifiedDay = array();

        foreach($weatherData as $data){
            if($day == date('Y-m-d', strtotime($data['forecastTimeUtc']))){
                $weathersAtSpecifiedDay[] = $data['conditionCode'];
            }
        }
        return $weathersAtSpecifiedDay;
    }

    /**
     * Function which requests products data from the database.
     * Data is saved to cache for 5 minutes if it is not already there.
     * @return Product[]|Collection|mixed products objects
     */
    public function getProductsDataFromDatabase()
    {
        $cacheTime = 300;
        $cacheKey = 'products';
        if (Cache::has($cacheKey)){
            return Cache::get($cacheKey);
        } else{
            $data = Product::all();
            Cache::add($cacheKey, $data, $cacheTime);
            return $data;
        }
    }
}
