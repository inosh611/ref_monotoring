<?php
namespace Modules\Orders\Providers;

use Illuminate\Support\ServiceProvider;

use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;
use Modules\Orders\Repositories\OrderRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
    
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);

    }

    public function boot()
    {
        
    }
}