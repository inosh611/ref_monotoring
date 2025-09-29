<?php
namespace Modules\Product\Providers;

use Illuminate\Support\ServiceProvider;

use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;
use Modules\Orders\Repositories\OrderRepository;
use Modules\Product\Repositories\Interfaces\ProductPriceRepositoryInterface;
use Modules\Product\Repositories\Interfaces\ProductRepositoryInterface;
use Modules\Product\Repositories\Interfaces\UnitRepositoryInterface;
use Modules\Product\Repositories\ProductPriceRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\Product\Repositories\UnitRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
    
        $this->app->bind(UnitRepositoryInterface::class, UnitRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductPriceRepositoryInterface::class, ProductPriceRepository::class);

    }

    public function boot()
    {
        
    }
}