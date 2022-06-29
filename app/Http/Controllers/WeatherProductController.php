<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Services\WeatherService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class WeatherProductController extends Controller
{
    /**
     * Displays weather products recommendations.
     * @param string $city requested city
     * @return string
     */
    public function displayRecommendations(string $city) : string
    {
        $output = (new WeatherService)->getWeatherRecommendationsForCity($city);
        return sprintf('<pre>%s</pre>', $output);
    }

    /**
     * Function for API weather data request. Catching exception and throwing a message if specified city is not available.
     * Weather data is saved to cache for 5 minutes if it is not already there.
     * @param string $city requested city
     * @return mixed weather data
     */
    public function requestAndGetWeatherData(string $city){
        $httpClient = new Client();
        $cacheTime = 300;
        try {
            if (Cache::has($city)){
                $request = Cache::get($city);
            } else{
                $requestValue = $httpClient->get("https://api.meteo.lt/v1/places/${city}/forecasts/long-term");
                $request = json_decode((string)$requestValue->getBody(), true);
                Cache::add($city, $request, $cacheTime);
            }
        } catch (\Throwable $e) {
            $text = sprintf("Error ('%s'): city '%s' not available.", $e->getCode(), $city);
            throw new \InvalidArgumentException($text);
        }
        return $request;
    }
}
