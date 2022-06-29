<?php
declare(strict_types=1);
namespace Tests\Unit\Services;

use App\Models\Product;
use App\Services\WeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeatherServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * City that is not available from the API provider
     * @var string
     */
    public $unavailable_City = "london";

    /**
     * City that is available from the API provider
     * @var string
     */
    public $available_City = "kaunas";

    /**
     * Testing getWeatherRecommendationsForCity() - when the given city is not available by provider,
     * it should throw an exception, that it is not available (404).
     * @return void
     */
    public function testGetWeatherRecommendationsForCityGivenCityNotAvailableExpectException()
    {
        //Arrange
        $notAvailableCity = $this->unavailable_City;
        //Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Error (404): city \"". $notAvailableCity . "\" not available.");
        //Act
        (new WeatherService())->getWeatherRecommendationsForCity($notAvailableCity);
    }

    /**
     * Testing getWeatherRecommendationsForCity() - when the given city is available by provider,
     * but the database has no objects to recommend - it should throw an exception,
     * that no products are available (404).
     * @return void
     */
    public function testGetWeatherRecommendationsForCityNoProductsAvailableExpectException()
    {
        //Arrange
        $availableCity = $this->available_City;
        //Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("No recommended products available.");
        //Act
        (new WeatherService())->getWeatherRecommendationsForCity($availableCity);
    }

    /**
     * Testing getWeatherRecommendationsForCity() - when the given city is available by provider,
     * and the database has objects to recommend - it will return, without any error.
     * @return void
     */
    public function testGetWeatherRecommendationsForCityProductsAndCityAvailableSuccess()
    {
        //Arrange
        $availableCity = $this->available_City;
        Product::factory()->create();
        //Assert
        $this->expectNotToPerformAssertions();
        //Act
        (new WeatherService())->getWeatherRecommendationsForCity($availableCity);
    }
}
