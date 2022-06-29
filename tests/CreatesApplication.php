<?php
declare(strict_types=1);
namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        $createApp = function() {
            $app = require __DIR__.'/../bootstrap/app.php';
            //$app->loadEnvironmentFrom('.env.testing');
            $app->make(Kernel::class)->bootstrap();
            return $app;
        };

        $app = $createApp();
        if ($app->environment() !== 'testing') {
            $this->clearCache();
            $app = $createApp();
        }

        return $app;
    }

    /**
     * Clears Laravel cache.
     */
    protected function clearCache()
    {
        $commands = ['clear-compiled', 'cache:clear', 'view:clear', 'config:clear', 'route:clear'];
        foreach ($commands as $command) {
            Artisan::call($command);
        }
    }
}
