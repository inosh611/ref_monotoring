<?php
namespace Modules\Customers\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Customers\Repositories\Interfaces\CustomerRepositoryInterface;
use Modules\Customers\Repositories\CustomerRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
    
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);   
        

    }

    public function boot()
    {
        
    }
}