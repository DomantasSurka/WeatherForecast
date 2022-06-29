<?php
declare(strict_types=1);
namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\WeatherProductController;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WeatherProductControllerTest extends TestCase
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
     * Testing getProductsDataFromDatabase() - when requesting products from database few times,
     * first time products are taken directly from database, second time taken from cache.
     * Checking if they are the same, not changed or damaged.
     * @return void
     */
    public function testGetProductsDataFromDatabaseGettingProductsFewTimesProductsAreSame()
    {
        //Arrange
        Product::factory()->create();
        //Act
        $productsFirst = (new WeatherProductController())->getProductsDataFromDatabase();
        $productsNext = (new WeatherProductController())->getProductsDataFromDatabase();
        //Assert
        $this->assertEquals($productsFirst, $productsNext);
    }

    /**
     * Testing getProductsDataFromDatabase() - requesting products from database,
     * and checking if it is saved to cache.
     * @return void
     */
    public function testGetProductsDataFromDatabaseGettingAndCheckingIfSavedToCacheDataIsSavedToCache()
    {
        //Arrange
        Product::factory()->create();
        //Act
        (new WeatherProductController())->getProductsDataFromDatabase();
        //Assert
        $this->assertTrue(Cache::has('products'));
    }

    /**
     * Testing getProductsDataFromDatabase() - checking cache before requesting products from database,
     * and after. Data should not be already saved to cache before requesting, but after - it should.
     * @return void
     */
    public function testGetProductsDataFromDatabaseCheckingBeforeAfterGettingIfSavedToCacheBeforeFalseAfterTrue()
    {
        //Arrange
        Product::factory()->create();
        //Act
        $beforeAdding = Cache::has('products');
        (new WeatherProductController())->getProductsDataFromDatabase();
        $afterAdding = Cache::has('products');
        //Assert
        $this->assertFalse($beforeAdding);
        $this->assertTrue($afterAdding);
    }

    /**
     * Testing requestAndGetWeatherData() - when requesting API few times,
     * first time API data are taken directly from its provider, second time taken from cache.
     * Checking if they are the same, not changed or damaged.
     * @return void
     */
    public function testRequestAndGetWeatherDataGettingDataFewTimesSameData()
    {
        //Arrange
        Product::factory()->create();
        $city = $this->available_City;
        //Act
        $dataFirst = (new WeatherProductController())->requestAndGetWeatherData($city);
        $dataNext = (new WeatherProductController())->requestAndGetWeatherData($city);
        //Assert
        $this->assertEquals($dataFirst, $dataNext);
    }

    /**
     * Testing requestAndGetWeatherData() - requesting data from provider,
     * and checking if it is saved to cache.
     * @return void
     */
    public function testRequestAndGetWeatherDataGettingAndCheckingIfSavedToCacheDataIsSavedToCache()
    {
        //Arrange
        Product::factory()->create();
        $city = $this->available_City;
        //Act
        (new WeatherProductController())->requestAndGetWeatherData($city);
        //Assert
        $this->assertTrue(Cache::has($city));
    }

    /**
     * Testing requestAndGetWeatherData() - checking cache before requesting data from provider,
     * and after. Data should not be already saved to cache before requesting, but after - it should.
     * @return void
     */
    public function testRequestAndGetWeatherDataCheckingBeforeAfterGettingIfSavedToCacheBeforeFalseAfterTrue()
    {
        //Arrange
        Product::factory()->create();
        $city = $this->available_City;
        //Act
        $beforeAdding = Cache::has($city);
        (new WeatherProductController())->requestAndGetWeatherData($city);
        $afterAdding = Cache::has($city);
        //Assert
        $this->assertFalse($beforeAdding);
        $this->assertTrue($afterAdding);
    }

    /**
     * Testing requestAndGetWeatherData() - when the given city is not available by provider,
     * it should throw an exception, that it is not available (404).
     * @return void
     */
    public function testRequestAndGetWeatherDataGivenCityNotAvailableExpectException()
    {
        //Arrange
        $notAvailableCity = $this->unavailable_City;
        //Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Error (404): city \"". $notAvailableCity . "\" not available.");
        //Act
        (new WeatherProductController())->requestAndGetWeatherData($notAvailableCity);
    }
}
