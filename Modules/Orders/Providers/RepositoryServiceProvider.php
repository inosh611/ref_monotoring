<?php
namespace Modules\Orders\Providers;

use Illuminate\Support\ServiceProvider;

use Modules\Orders\Repositories\ItemRepository;
use Modules\Orders\Repositories\OrderRepository;
use Modules\Orders\Repositories\Interfaces\ItemRepositoryInterface;
use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
    
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(ItemRepositoryInterface::class, ItemRepository::class);

    }

    public function boot()
    {
        
    }
}